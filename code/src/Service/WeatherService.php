<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private HttpClientInterface $client;
    private string $apiBaseUrl;
    private string $apiUrl;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params)
    {
        $this->client = $client;
        $this->apiBaseUrl = $params->get('WEATHER_API_URL');

    }
    public function getWeather(): array
    {
        $fakeJson = __DIR__ . '/../../FakeJSON.json';
        if(!file_exists($fakeJson)) {
            die("Erreur : fichier n'existe pas");
        }
        $jsonData = file_get_contents($fakeJson);
        $data = json_decode($jsonData, true);
        $weatherData = [];

        if(isset($data['users']) && is_array($data['users']))
        {
            foreach ($data['users'] as $user)
            {
                $userId = $user['userID'];
                $latitude = $user['userLocation']['coordinates']['X'];
                $longitude = $user['userLocation']['coordinates']['Y'];

                $this->apiUrl = $this->apiBaseUrl
                    . "?latitude={$latitude}&longitude={$longitude}"
                    . "&current_weather=true&hourly=temperature_2m,relative_humidity_2m,precipitation"
                    . "&daily=temperature_2m_max,temperature_2m_min,sunshine_duration"
                    . "&timezone=auto";

                $response = $this->client->request('GET', $this->apiUrl);
                if ($response->getStatusCode() !== 200) {
                    throw new \Exception("Erreur lors de la recuperation des donnees meteo : " . $response->getContent(false));
                }
                $weatherData[$userId] = $response->toArray();
            }
        }
        return $weatherData;
    }
}