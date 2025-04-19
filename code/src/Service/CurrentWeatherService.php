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
        $currentDateTime->modify('+1 day');
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


    public function filterWeatherByLandId(array $weatherData, string $landId): array
    {
        $weatherDetails = [];

        if (!isset($weatherData['Users'])) {
            return [];
        }

        foreach ($weatherData['Users'] as $user) {
            foreach ($user['Farms'] as $farm) {
                foreach ($farm['Lands'] as $land) {
                    if ($land['LandId'] != $landId || !isset($land['Meteo'])) {
                        continue;
                    }

                    $currentTimeIndex = $this->getCurrentTimeIndex($land['Meteo']['hourly']);

                    $weatherDetails[] = [
                        'user_id' => $user['UserId'],
                        'farm_id' => $farm['FarmId'],
                        'land_id' => $landId,
                        'plant_id' => $land['PlantId'],
                        'humidity' => $land['Meteo']['hourly']['relative_humidity_2m'][$currentTimeIndex] ?? null,
                        'temperature' => $land['Meteo']['hourly']['temperature_2m'][$currentTimeIndex] ?? null,
                        'precipitation' => $land['Meteo']['hourly']['precipitation'][$currentTimeIndex] ?? null,
                        'wind_speed' => $land['Meteo']['hourly']['wind_speed_10m'][$currentTimeIndex] ?? null,
                    ];

                    return $weatherDetails; // return early since we found the land
                }
            }
        }

        return $weatherDetails;
    }
    


    public function filterWeatherByUserIdForWeekly(array $weatherData, string $userId): array
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


                    $weatherDetails[] = [
                        'user_id' => $userId,
                        'farm_id' => $farmId,
                        'land_id' => $landId,
                        'plant_id' => $plantId,                    
                        'dates' => $meteoData['daily']['time']                 
                    ];
                }
            }
        }

        return $weatherDetails;
    }


    public function filterWeatherByLandIdForWeekly(array $weatherData, string $landId): array
    {
        $weatherDetails = [];

        if (!isset($weatherData['Users'])) {
            return [];
        }

        foreach ($weatherData['Users'] as $user) {
            foreach ($user['Farms'] as $farm) {
                foreach ($farm['Lands'] as $land) {
                    if ($land['LandId'] != $landId || !isset($land['Meteo'])) {
                        continue;
                    }

                    $meteoData = $land['Meteo'];
                    $plantId = $land['PlantId'];

                    $weatherDetails[] = [
                        'user_id' => $user['UserId'],
                        'farm_id' => $farm['FarmId'],
                        'land_id' => $landId,
                        'plant_id' => $plantId,
                        'dates' => $meteoData['daily']['time']
                    ];

                    return $weatherDetails; // Return once the matching land is found
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