<?php

namespace App\Controller;

use App\Repository\AdviceRepository;
use App\Repository\LandPlantsRepository;
use App\Repository\LandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api/current-advice')]
final class CurrentAdviceController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private LandRepository $landRepo;
    private LandPlantsRepository $LandPlantRepo;
    private AdviceRepository $adviceRepo;

    public function __construct(HttpClientInterface $httpClient , LandRepository $landRepo , LandPlantsRepository $LandPlantRepo , AdviceRepository $adviceRepo) {
        $this->httpClient = $httpClient;
        $this->landRepo = $landRepo;
        $this->LandPlantRepo = $LandPlantRepo;
        $this->adviceRepo = $adviceRepo;
    }


    //! change implementation to many temps (json will return farm => lands => temp in each land)
    #[Route('/{userId}', methods:['GET'])]
    public function getCurrentAdvice(int $userId): JsonResponse
    {
        //? 1- Fetch weather for a user (on suppose that Tempeture is same for all lands) =====
        $weatherData = $this->fetchWeatherFromApi($userId);

        if (!$weatherData || !isset($weatherData['temperature'])) {
            return new JsonResponse(['error' => 'Failed to fetch weather data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        //! select based on ayman's Json 
        $currentTemp = $weatherData['temperature'];


        //? 2️- Find user's lands ======
        $lands = $this->landRepo->findBy(['user_id' => $userId]);
        if (!$lands) {
            return $this->json(['error' => 'No lands found for this user'], JsonResponse::HTTP_NOT_FOUND);
        }

        $adviceList = [];

        foreach($lands as $land){
            $landPlants = $this->LandPlantRepo->findPlantsByLand($land->getId());

            foreach($landPlants as $landPlant){
                $landPlantId = $landPlant->getId();

                // Get advice using land_plant_id
                $advices = $this->adviceRepo->findByTemperatureRange($landPlantId, $currentTemp);

                foreach ($advices as $advice) {
                    $adviceList[] = [
                        'land_id' => $land->getId(),
                        'land_plant_id' => $landPlantId,
                        'plant_id' => $landPlant->getPlant()->getId(),
                        'plant_name' => $landPlant->getPlant()->getName(),
                        'temp' => $currentTemp,
                        'advice_text_en' => $advice->getAdviceTextEn(),
                        'advice_text_fr' => $advice->getAdviceTextFr(),
                        'advice_text_ma' => $advice->getAdviceTextAr(),
                        'AudioPath'=> $advice->getAudioPath(),
                        'RedAlert' => $advice->isRedAlert()
                    ];
                }
            }
        }

        return $this->json($adviceList);
    }



    private function fetchWeatherFromApi(int $userId): ?array
    {
        // $weatherApiUrl = "ayman's api/$userId";

        // try {
        //     $response = $this->httpClient->request('GET', $weatherApiUrl);
        //     return $response->toArray();

        // } catch (\Exception $e) {
        //     return ['errors :' => $e];
        // }

        $weatherData = [
                "temperature" => 14,
                "humidity" => 60,
                "wind_speed" => 5.2,
                "condition" => "Sunny"
        ];
        
        return $weatherData;
    
    }

}
