<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

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
            if(empty($weather))
            {
                throw new \Exception("aucune donnee meteo recuperee");
            }
            $cacheItem = $this->cache->getItem('cache_weather');
            $cacheItem->set($weather);
            $cacheItem->expiresAfter(12 * 3600);
            $this->cache->save($cacheItem);

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