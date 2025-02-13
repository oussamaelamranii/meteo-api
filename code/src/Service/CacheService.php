<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheService
{
    private WeatherService $weatherService;
    private CacheInterface $cache;
    private LoggerInterface $logger;

    public function __construct(WeatherService $weatherService, CacheInterface $cache, LoggerInterface $logger)
    {
        $this->weatherService = $weatherService;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function storeWeatherInCache(): void
    {
        try {
            $weather = $this->weatherService->getWeather();

            $this->cache->get('cache_weather', function(ItemInterface $item) use ($weather) {
                $item->expiresAfter(12 * 3600);
                return $weather;
            });
            $this->logger->info("donnees meteo stockees dans le cache : ");

        } catch (\Exception $e) {
            $this->logger->error("erreur lors du stockage des donnees meteo dans le cache : " . $e->getMessage());
        }
    }

    public function getWeatherFromCache(): ?array
    {
        return $this->cache->get('cache_weather', function(){
            return null;
        });
    }

}