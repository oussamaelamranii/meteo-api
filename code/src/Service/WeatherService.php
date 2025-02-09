<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private HttpClientInterface $client;
    private CacheInterface $cache;
    private LoggerInterface $logger;
    private string $apiUrl;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiUrl = 'https://api.open-meteo.com/v1/forecast?latitude=52.52&longitude=13.41'
            . '&current_weather=true&hourly=temperature_2m,relative_humidity_2m,precipitation'
            . '&daily=temperature_2m_max,temperature_2m_min,sunshine_duration'
            . '&timezone=auto';
    }
    public function getWeather(): array
    {
        $response = $this->client->request('GET', $this->apiUrl);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("Erreur lors de la récupération des données météo : " . $response->getContent(false));
        }

        return $response->toArray();
    }

    public function storeWeatherInCache(): void
    {
        $weather = $this->getWeather();

        $this->cache->get('cache_weather', function(ItemInterface $item) use ($weather)
        {
            $item->expiresAfter(12 * 3600);
            return $weather;
        });
    }

    public function getWeatherFomCache(): array
    {
        return $this->cache->get('cache_weather', function(ItemInterface $item)
        {
            return null;
        });
    }
}