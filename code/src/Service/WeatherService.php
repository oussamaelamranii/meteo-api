<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private HttpClientInterface $client;
    private string $apiBaseUrl;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params)
    {
        $this->client = $client;
        $this->apiBaseUrl = $params->get('WEATHER_API_URL');
    }
    public function buildWeatherApiUrl(float $latitude, float $longitude): string
    {
        return $this->apiBaseUrl
            . "?latitude={$latitude}&longitude={$longitude}"
            . "&current_weather=true"
            . "&hourly=temperature_2m,relative_humidity_2m,precipitation,wind_speed_10m,soil_temperature_18cm,soil_moisture_9_to_27cm"
            . "&daily=temperature_2m_max,temperature_2m_min,sunshine_duration"
            . "&timezone=auto";
    }
    public function getWeather(): array
    {
        $fakeJson = __DIR__ . '/../../FakeJSON.json';
        if(!file_exists($fakeJson)) {
            throw new \RuntimeException("Erreur : fichier n'existe pas");
        }
        $jsonData = file_get_contents($fakeJson);
        $data = json_decode($jsonData, true);

        if(!isset($data['Users']) || !is_array($data['Users'])) {
            throw new \RuntimeException("Erreur : donnees invalides! ");
        }
        $weatherData = [];
        foreach ($data['Users'] as $user)
        {
            $userId = $user['UserId'];
            foreach($user['Farms'] as $farm)
            {
                $farmId = $farm['FarmId'];
                foreach($farm['Lands'] as $land)
                {
                    $landId = $land['LandId'];
                    $latitude = $land['CenterX'];
                    $longitude = $land['CenterY'];
                    $response = $this->client->request('GET', $this->buildWeatherApiUrl($latitude, $longitude));

                    if ($response->getStatusCode() !== 200) {
                        throw new \Exception("Erreur lors de la recuperation des donnees meteo : " . $response->getContent(false));
                    }
                    $weatherData[$userId][$farmId][$landId] = $response->toArray();
                }
            }
        }
        return $weatherData;
    }
    /*public function getWeatherByUser(int $id): array
    {
        $fakeJson = __DIR__ . '/../../FakeJSON.json';
        if(!file_exists($fakeJson)) {
            throw new \RuntimeException("Erreur : fichier n'existe pas");
        }
        $jsonData = file_get_contents($fakeJson);
        $data = json_decode($jsonData, true);
        if(!isset($data['Farm']) || !is_array($data['Farm'])) {
            throw new \RuntimeException("Erreur : donnees invalides! ");
        }
        $weatherData = [];
        $existingId = false;
        foreach($data['Farm'] as $farm)
        {
            $farmId = $farm['FarmID'];
            $userId = $farm['UserID'];
            if($userId == $id)
            {
                $existingId = true;
                foreach($farm['Land'] as $land)
                {
                    $landId = $land['LandId'];
                    $latitude = $land['LatitudeCenter'];
                    $longitude = $land['LongitudeCenter'];
                    $response = $this->client->request('GET', $this->buildWeatherApiUrl($latitude, $longitude));

                    if ($response->getStatusCode() !== 200) {
                        throw new \Exception("Erreur lors de la recuperation des donnees meteo : " . $response->getContent(false));
                    }
                    $weatherData[$farmId][$landId] = $response->toArray();
                }
            }
        }
        if(!$existingId) throw $this->createNotFoundException("user not found!");
        return $weatherData;
    }*/
}