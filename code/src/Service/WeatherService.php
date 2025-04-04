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
            . "&current=temperature_2m,wind_speed_10m,relative_humidity_2m,precipitation,rain,wind_direction_10m"
            . "&hourly=temperature_2m,relative_humidity_2m,precipitation,wind_speed_10m,soil_temperature_18cm,soil_moisture_9_to_27cm"
            . "&daily=temperature_2m_max,wind_speed_10m_max,precipitation_sum"
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
        foreach ($data['Users'] as &$user)
        {
            foreach($user['Farms'] as &$farm)
            {
                foreach($farm['Lands'] as &$land)
                {
                    $latitude = $land['CenterX'];
                    $longitude = $land['CenterY'];
                    try{
                        $response = $this->client->request('GET', $this->buildWeatherApiUrl($latitude, $longitude));
                        if ($response->getStatusCode() !== 200) {
                            throw new \Exception("Erreur lors de la recuperation des donnees meteo : " . $response->getContent(false));
                        }
                        $land['Meteo'] = $response->toArray();
                    }catch (\Exception $exception){
                        $land['Meteo'] = ['error' => $exception->getMessage()];
                    }
                }
            }
        }
        return $data;
    }
}