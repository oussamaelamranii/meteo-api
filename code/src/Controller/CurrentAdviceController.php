<?php

namespace App\Controller;

use App\Repository\AdviceRepository;
use App\Repository\PlantsRepository;
use App\Service\CurrentWeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/current-advice')]
final class CurrentAdviceController extends AbstractController
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


    #[Route('/all', methods:['GET'] , priority:1)]
    public function getCurrentAdviceForAllUsers(): JsonResponse
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

        $userWeatherData = $this->CurrAdvice->getAllUsersWeather($weatherData);

        // dd($userWeatherData);

        if (empty($userWeatherData)) {
            return $this->json(['error' => 'No weather data found for this user'], JsonResponse::HTTP_NOT_FOUND);
        }

        $adviceList = [];

        foreach ($userWeatherData as $landWeather) {
            
            // dd($landWeather);

            $userId = $landWeather['user_id'];
            $farmId = $landWeather['farm_id'];
            $landId = $landWeather['land_id'];
            $humidity = $landWeather['humidity'];
            $precipitation = $landWeather['precipitation'];
            $windSpeed = $landWeather['wind_speed'];
            $temperature = $landWeather['temperature'];


            $advices = $this->adviceRepo->findByWeatherConditions(
                $landId, 
                $temperature, 
                $humidity, 
                $precipitation, 
                $windSpeed
            );


            foreach ($advices as $advice) {
                $adviceList[] = [
                    'user_id' => $userId,
                    'farm_id' => $farmId,
                    'land_id' => $landId,
                    'plant_id' => $advice->getPlant()->getId(),
                    'humidity' => $humidity,
                    'precipitation' => $precipitation,
                    'wind_speed' => $windSpeed,
                    'temperature' => $temperature,
                    'advice_text_en' => $advice->getAdviceTextEn(),
                    'advice_text_fr' => $advice->getAdviceTextFr(),
                    'advice_text_ma' => $advice->getAdviceTextAr(),
                    'AudioPathAr' => $advice->getAudioPathAr(),
                    'RedAlert' => $advice->isRedAlert(),
                ];
            }
        }

        return $this->json($adviceList);
    }


    #[Route('/{userId}', methods:['GET'], requirements: ['userId' => '\d+'])]
    public function getCurrentAdviceForUser(int $userId): JsonResponse
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


        $userWeatherData = $this->CurrAdvice->filterWeatherByUserId($weatherData, $userId);

        // dd($userWeatherData);

        if (empty($userWeatherData)) {
            return $this->json(['error' => 'No weather data found for this user'], JsonResponse::HTTP_NOT_FOUND);
        }

        $adviceList = [];

        foreach ($userWeatherData as $landWeather) {
            // dd($landWeather);

            $userId = $landWeather['user_id'];
            $farmId = $landWeather['farm_id'];
            $landId = $landWeather['land_id'];
            $plantId = $landWeather['plant_id'];
            $humidity = $landWeather['humidity'];
            $precipitation = $landWeather['precipitation'];
            $windSpeed = $landWeather['wind_speed'];
            $temperature = $landWeather['temperature'];


            $advices = $this->adviceRepo->findByWeatherConditions(
                $landId, 
                $temperature, 
                $humidity, 
                $precipitation, 
                $windSpeed
            );

            foreach ($advices as $advice) {
                $adviceList[] = [
                    'user_id' => $userId,
                    'land_id' => $landId,
                    'plant_id' =>$advice->getPlant()->getId(),
                    'humidity' => $humidity,
                    'precipitation' => $precipitation,
                    'wind_speed' => $windSpeed,
                    'temperature' => $temperature,
                    'advice_text_en' => $advice->getAdviceTextEn(),
                    'advice_text_fr' => $advice->getAdviceTextFr(),
                    'advice_text_ma' => $advice->getAdviceTextAr(),
                    'AudioPathEn' => $advice->getAudioPathEn(),
                    'AudioPathFr' => $advice->getAudioPathFr(),
                    'AudioPathAr' => $advice->getAudioPathAr(),
                    'RedAlert' => $advice->isRedAlert(),
                ];
            }
        }

        return $this->json($adviceList);
    }



}
