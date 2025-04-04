<?php

namespace App\Controller;

use App\Repository\AdviceRepository;
use App\Repository\PlantsRepository;
use App\Service\CurrentWeatherService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[Route('/api/weekly-advice')]
final class WeeklyAdviceController extends AbstractController
{
    private AdviceRepository $adviceRepo;
    private PlantsRepository $plantRepo;
    private CurrentWeatherService $CurrAdvice;

    public function __construct(
                        AdviceRepository $adviceRepo, 
                        CurrentWeatherService $CurrAdvice,
                        PlantsRepository $plantRepo
                        ) {

        $this->adviceRepo = $adviceRepo;
        $this->CurrAdvice = $CurrAdvice;
        $this->plantRepo = $plantRepo;
    }
    

    #[Route('/{userId}', methods:['GET'], requirements: ['userId' => '\d+'])]
    public function getWeeklyAdviceForUser(int $userId): JsonResponse
    {   

        //* //////////////// this is the code that should be used  ! /////////////////////
        // Fetch weather data for the user
        $weatherData = $this->CurrAdvice->getWeatherAllCache();
        // dd($weatherData);

        if (!$weatherData) {
            return new JsonResponse(['error' => 'Failed to fetch weather data'], JsonResponse::HTTP_BAD_REQUEST);
        }        
        //* //////////////////////////////////////////////////////////


        //! //////////////// Just for Testing ! /////////////////////
            // $jsonFile = $this->getParameter('kernel.project_dir') . '/var/CacheJson.json';
            // $weatherData = json_decode(file_get_contents($jsonFile), true);
        //! //////////////// Just for Testing ! /////////////////////


        $userWeatherData = $this->CurrAdvice->filterWeatherByUserIdForWeekly($weatherData, $userId);

        // dd($userWeatherData);

        if (empty($userWeatherData)) {
            return $this->json(['error' => 'No weather data found for this user'], JsonResponse::HTTP_NOT_FOUND);
        }


        $adviceList = [];

        foreach ($userWeatherData as $landWeather) {
            // dump($landWeather['land_id']); //? check if land_id is changing !
            $DailyAdvices = [];

            $userId = $landWeather['user_id'];
            $farmId = $landWeather['farm_id'];
            $landId = $landWeather['land_id'];
            $plantId = $landWeather['plant_id'];
            $dates = $landWeather['dates'];

            foreach ($dates as $date) {
                    $dateObject = new \DateTime($date);     
                    $dateObject = $dateObject->format("Y-m-d");
                    
                    // dump($dateObject."/".$landId);
            
                    $advices = $this->adviceRepo->findByWeatherDates(
                        $landId,
                        $dateObject
                    );

                    foreach ($advices as $advice) {
                            $DailyAdvices[] = [
                                'advice_text_en' => $advice->getAdviceTextEn(),
                                'advice_text_fr' => $advice->getAdviceTextFr(),
                                'advice_text_ma' => $advice->getAdviceTextAr(),
                                'AudioPathEn' => $advice->getAudioPathEn(),
                                'AudioPathFr' => $advice->getAudioPathFr(),
                                'AudioPathAr' => $advice->getAudioPathAr(),
                                'RedAlert' => $advice->isRedAlert(),
                                'adviceDate' => $advice->getAdviceDate(),
                                'createdAt' => $advice->getCreatedAt(),
                            ];
                    }
            }

                $adviceList[] = [
                    'user_id' => $userId,
                    'farm_id' => $farmId,
                    'land_id' => $landId,
                    'plant_id' => $plantId,
                    'advices'=> $DailyAdvices
                ];
    }
        
    return $this->json($adviceList);
    
}
}
