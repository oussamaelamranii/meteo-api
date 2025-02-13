<?php

namespace App\Controller;

use App\Service\CacheService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class WeatherCacheController extends AbstractController
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    #[Route('/weather-cache', name: 'weather_cache', methods: ['GET'])]
    public function getWeatherCache(): JsonResponse
    {
        $weatherData = $this->cacheService->getWeatherFromCache();

        if(empty($weatherData))
        {
            return $this->json([
                'status' => 404,
                'error' => 'les donnees ne sont pas disponibles dans le cache'
            ], 404);
        }

        return $this->json($weatherData);
    }
}
// docker exec -it meteo-api-redis-1 redis-cli
//  GET CUC8h6Dc2D: