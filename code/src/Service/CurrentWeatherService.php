<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CurrentWeatherService
{
    private HttpClientInterface $httpClient;
    private CacheService $cacheService;

    

    public function __construct(HttpClientInterface $httpClient , CacheService $cacheService)
    {
        $this->httpClient = $httpClient;
        $this->cacheService = $cacheService;
    }


    public function getWeatherAllCache(): array
    {
        $weatherData = $this->cacheService->getWeatherFromCache();

        if (empty($weatherData)) {
            $this->cacheService->storeWeatherInCache();
            $weatherData = $this->cacheService->getWeatherFromCache();

            if (empty($weatherData)) {
                return [
                    'status' => 503,
                    'error' => 'Erreur temporaire, les donnees ne sont pas disponibles dans le cache'
                ];
            }
        }
        
        return $weatherData;
    }


    // search current time in api time array
    public function getCurrentTimeIndex(array $landData): ?int
    {
        $currentDateTime = new \DateTime('now', new \DateTimeZone('UTC')); 
        // $currentDateTime->modify('+1 hour'); //! avoid Nulls 
        $formattedTime = $currentDateTime->format('Y-m-d\TH:00');
        // $formattedTime = '2025-03-20T11:00';

        // var_dump($formattedTime);
        // var_dump($landData['time']);

        $timeIndex = array_search($formattedTime, $landData['time']);
        
        return $timeIndex !== false ? $timeIndex : null;
    }


    public function filterWeatherByUserId(array $weatherData, string $userId): array
    {

            $weatherDetails = [];

            if (!isset($weatherData['Users'])) {
                return [];
            }

            foreach ($weatherData['Users'] as $user) {
                if ($user['UserId'] != $userId) {
                    continue;
                }
        
                foreach ($user['Farms'] as $farm) {
                    $farmId = $farm['FarmId'];
        
                    foreach ($farm['Lands'] as $land) {
                        $landId = $land['LandId'];
        
                        if (!isset($land['Meteo'])) {
                            continue;
                        }

                        $meteoData = $land['Meteo'];
                        $plantId = $land['PlantId'];

                    $currentTimeIndex = $this->getCurrentTimeIndex($meteoData['hourly']);

                    $weatherDetails[] = [
                        'user_id' => $userId,
                        'farm_id' => $farmId,
                        'land_id' => $landId,
                        'plant_id' => $plantId,
                        'humidity' => $meteoData['hourly']['relative_humidity_2m'][$currentTimeIndex] ?? null,
                        'temperature' => $meteoData['hourly']['temperature_2m'][$currentTimeIndex] ?? null,
                        'precipitation' => $meteoData['hourly']['precipitation'][$currentTimeIndex] ?? null,
                        'wind_speed' => $meteoData['hourly']['wind_speed_10m'][$currentTimeIndex] ?? null,
                    ];
                }
            }
        }

        return $weatherDetails;
    }



    public function getAllUsersWeather(array $weatherData): array
    {
        $weatherDetails = [];

        if (!isset($weatherData['Users'])) {
            return [];
        }

        foreach ($weatherData['Users'] as $user) {
            $userId = $user['UserId'];

            foreach ($user['Farms'] as $farm) {
                $farmId = $farm['FarmId'];

                foreach ($farm['Lands'] as $land) {
                    $landId = $land['LandId'];

                    if (!isset($land['Meteo'])) {
                        continue;
                    }

                    $meteoData = $land['Meteo'];
                    $plantId = $land['PlantId'];

                    $currentTimeIndex = $this->getCurrentTimeIndex($meteoData['hourly']);

                    $weatherDetails[] = [
                        'user_id' => $userId,
                        'farm_id' => $farmId,
                        'land_id' => $landId,
                        'plant_id' => $plantId,
                        'humidity' => $meteoData['hourly']['relative_humidity_2m'][$currentTimeIndex] ?? null,
                        'temperature' => $meteoData['hourly']['temperature_2m'][$currentTimeIndex] ?? null,
                        'precipitation' => $meteoData['hourly']['precipitation'][$currentTimeIndex] ?? null,
                        'wind_speed' => $meteoData['hourly']['wind_speed_10m'][$currentTimeIndex] ?? null,
                    ];
                }
            }
        }

        return $weatherDetails;
    }



}