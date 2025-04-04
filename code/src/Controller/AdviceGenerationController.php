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

        $dailyData = $userWeatherConditions;
        $generatedAdvices = [];

        // Loop through the daily weather data
        foreach ($dailyData['time'] as $index => $date) {
            // Extract the weather conditions for this day
            $dailyWeather = [
                'date' => $date,
                'temperature_max' => $dailyData['temperature_2m_max'][$index] ?? null,
                'wind_speed_max' => $dailyData['wind_speed_10m_max'][$index] ?? null,
                'precipitation_sum' => $dailyData['precipitation_sum'][$index] ?? null
            ];

            // var_dump($dailyWeather);

            // Generate advice for this day's weather
            $generatedAdvice = $this->adviceGeneration->GenerateWeeklySpecificAdvice($dailyWeather, $plant);

            // Insert the advice into the database
            $advice = $this->AdviceService->InsertWeeklySpecificAdvice($landId, $plant, $dailyWeather, $generatedAdvice);

            // Store response for final output
            $adviceData = json_decode($advice->getContent(), true);            
            
            sleep(12);

            $generatedAdvices[] = [
                'date' => $date,
                'advice_en' => $generatedAdvice,
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
            'Plant' => $plant,
            'Land' => $landId,
            'User' => $userId,
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
            'Plant'=> $plant,
            'Land'=> $landId,
            'User'=> $userId,
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
