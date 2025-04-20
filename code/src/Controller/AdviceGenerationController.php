<?php

namespace App\Controller;

use App\Entity\Land;
use App\Entity\Advice;
use App\Entity\Plants;
use App\Interface\RedAlertServiceInterface;
use App\Repository\AdviceRepository;
use App\Service\AdviceGenerationService;
use App\Service\AdviceService;
use App\Service\CurrentWeatherService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[Route('/api/generate_advice')]
final class AdviceGenerationController extends AbstractController
{

    private AdviceGenerationService $adviceGeneration;
    private AdviceService $AdviceService;
    private AdviceRepository $adviceRepo;
    private CurrentWeatherService $CurrAdvice;

    public function __construct(
        AdviceGenerationService $adviceGeneration, 
        AdviceService $AdviceService , 
        CurrentWeatherService $CurrAdvice, 
        AdviceRepository $adviceRepo)
        {
            $this->adviceGeneration = $adviceGeneration;
            $this->AdviceService = $AdviceService;
            $this->CurrAdvice = $CurrAdvice;
            $this->adviceRepo = $adviceRepo;
    }


    #[Route('/weekly_advice/{userId}/{farmId}/{landId}/{plant}', methods:['GET'])]
    public function GenerateSpecificAdviceForNextSevenDays(int $userId , int $farmId , int $landId , string $plant): JsonResponse
    {
        // Fetch weather data for the user
        $weatherData = $this->CurrAdvice->getWeatherAllCache();

        if (!$weatherData) {
            return new JsonResponse(['error' => 'Failed to fetch weather data'], JsonResponse::HTTP_BAD_REQUEST);
        }        

        // Filter weather data for the user's farm and land
        $userWeatherConditions = $this->adviceGeneration->filterWeatherByUserFarmLandWeekly($weatherData, $userId, $farmId, $landId);

        // Ensure the daily array exists
        if (!isset($userWeatherConditions)) {
            return new JsonResponse(['error' => 'Invalid weather data format'], JsonResponse::HTTP_BAD_REQUEST);
        }

        //* here split /////////////////////////////////////////////////////
        $dailyData = $userWeatherConditions;
        $generatedAdvices = [];
        
        // Step 1: prepare 7-day weather data
        $weeklyWeather = [];
        foreach ($dailyData['time'] as $index => $date) {
            $weeklyWeather[] = [
                'date' => $date,
                'temperature_max' => $dailyData['temperature_2m_max'][$index] ?? null,
                'wind_speed_max' => $dailyData['wind_speed_10m_max'][$index] ?? null,
                'precipitation_sum' => $dailyData['precipitation_sum'][$index] ?? null
            ];
        }
        
        // Step 2: call OpenAI once
        $openAiResponse = $this->adviceGeneration->GenerateWeeklySpecificAdvice($weeklyWeather, $plant);
        
        // Step 3: split into 7 advices
        $advices = explode('|||', $openAiResponse);
        
        // Step 4: save each advice to DB and prepare output
        foreach ($weeklyWeather as $i => $dailyWeather) {
            $adviceText = trim($advices[$i] ?? 'No advice');
        
            // Save to DB
            $advice = $this->AdviceService->InsertWeeklySpecificAdvice($landId, $plant, $dailyWeather, $adviceText);
            
            sleep(30);
        
            $adviceData = json_decode($advice->getContent(), true); 

            // dump($adviceData);
        
            $generatedAdvices[] = [
                'date' => $dailyWeather['date'],
                'advice_en' => $adviceText,
                'advice_fr' => $adviceData['adviceTextFr'],
                'advice_ar' => $adviceData['adviceTextAr'],
                'audioPathEn' => $adviceData['audioPathEn'],
                'audioPathAr' => $adviceData['audioPathAr'],
                'audioPathFr' => $adviceData['audioPathFr'],
                'createdAt' => $adviceData['createdAt']
            ];
        }

        //! delete old advices
        $this->adviceRepo->deleteOldAdvices();

        return $this->json([
            'User' => $userId,
            'Farm' => $farmId,
            'Land' => $landId,
            'Plant' => $plant,
            'WeatherConditions' => $userWeatherConditions,
            'GeneratedAdvices' => $generatedAdvices
        ]);
    }




    #[Route('/specific_advice/{userId}/{farmId}/{landId}/{plant}', methods:['GET'])]
    public function GenerateSpecificAdvice(int $userId , int $farmId , int $landId , string $plant): JsonResponse
    {
        // * //////////////// this is the code that should be used  ! /////////////////////
            // Fetch weather data for the user
            $weatherData = $this->CurrAdvice->getWeatherAllCache();           

            if (!$weatherData) {
                return new JsonResponse(['error' => 'Failed to fetch weather data'], JsonResponse::HTTP_BAD_REQUEST);
            }        
        // * //////////////////////////////////////////////////////////

        // //! //////////////// Just for Testing ! /////////////////////
            // $jsonFile = $this->getParameter('kernel.project_dir') . '/var/CacheJson.json';
            // $weatherData = json_decode(file_get_contents($jsonFile), true);
            // dd($weatherData);
        // //! //////////////// Just for Testing ! /////////////////////

        $userWeatherConditions = $this->adviceGeneration->filterWeatherByUserFarmLand($weatherData , $userId , $farmId , $landId);

        // dd($userWeatherConditions);

        $GeneratedAdvice = $this->adviceGeneration->GenerateSpecificAdvice($userWeatherConditions , $plant);

        //* insert generated advice into advice table
        $advice = $this->AdviceService->InsertSpecificAdvice($landId , $plant , $userWeatherConditions , $GeneratedAdvice);
        
        $data = json_decode($advice->getContent() , true);        
        
        // dd(json_decode($advice->getContent(), true));

        return $this->json([
            'User' => $userId,
            'Farm' => $farmId,
            'Land' => $landId,
            'Plant' => $plant,
            'WeatherConditions'=> $userWeatherConditions,
            'Advice_en'=> $GeneratedAdvice,
            'Advice_fr'=> $data['adviceTextFr'],
            'Advice_ar'=> $data['adviceTextAr'],
            'audioPathEn'=> $data['audioPathEn'],
            'audioPathAr'=> $data['audioPathAr'],
            'audioPathFr'=> $data['audioPathFr'],
            'createdAt'=> $data['createdAt']
        ]);
    }




    #[Route('/general_advice/{plant}', methods:['GET'])]
    public function GenerateGeneralAdvice(string $plant): JsonResponse
    {
        $GeneratedAdvice = $this->adviceGeneration->GenerateGeneralAdvice($plant);

        $this->AdviceService->InsertGeneralAdvice($plant , $GeneratedAdvice);

        return $this->json([
            'Plant'=> $plant,
            'GeneratedAdvice'=> $GeneratedAdvice
        ]);
    }
}
