<?php

namespace App\Service;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private HttpClientInterface $client;
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

}