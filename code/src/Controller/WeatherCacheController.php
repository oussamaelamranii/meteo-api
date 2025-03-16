<?php

namespace App\Controller;

use App\Service\CacheService;
use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class WeatherCacheController extends AbstractController
{
    private CacheService $cacheService;
    private WeatherService $weatherService;
    public function __construct(CacheService $cacheService, WeatherService $weatherService)
    {
        $this->cacheService = $cacheService;
        $this->weatherService = $weatherService;
    }

    #[Route('/weather-all-cache', name: 'weather_cache', methods: ['GET'])]
    public function getWeatherAllCache(): JsonResponse
    {
        $weatherData = $this->cacheService->getWeatherFromCache();

            if(empty($weatherData))
            {
                $this->cacheService->storeWeatherInCache();
                $weatherData = $this->cacheService->getWeatherFromCache();

                if(empty($weatherData))
                {
                    return $this->json([
                        'status' => 503,
                        'error' => 'Erreur temporaire, les donnees ne sont pas disponibles dans le cache'
                    ], 503);
                }
            }
        return $this->json($weatherData);
    }

    #[Route('/weather', name: 'weather_api', methods: ['GET'])]
    public function getWeatherFromApi(): JsonResponse
    {
        $data = $this->weatherService->getWeather();
        return $this->json($data);
    }
}