<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private HttpClientInterface $client;
    private string $apiBaseUrl;
    private UserService $userService;
    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, UserService $userService)
    {
        $this->client = $client;
        $this->apiBaseUrl = $params->get('WEATHER_API_URL');
        $this->userService = $userService;
    }
    public function buildWeatherApiUrl(float $latitude, float $longitude): string
    {
        return $this->apiBaseUrl
            . "?latitude={$latitude}&longitude={$longitude}"
            . "&current=temperature_2m,apparent_temperature,wind_speed_10m,relative_humidity_2m,precipitation,cloud_cover,wind_direction_10m"
            . "&hourly=temperature_2m,relative_humidity_2m,precipitation,wind_speed_10m,soil_temperature_18cm,soil_moisture_9_to_27cm,precipitation_probability,is_day,cloud_cover"
            . "&daily=temperature_2m_max,temperature_2m_min,sunshine_duration,precipitation_sum,sunrise,sunset"
            . "&timezone=auto";
    }
    public function getWeather(): array
    {
//        $fakeJson = __DIR__ . '/../../FakeJSON.json'; // 5 lands * 2 farms * 20 users
//        if(!file_exists($fakeJson)) {
//            throw new \RuntimeException("Erreur : fichier n'existe pas");
//        }
//        $jsonData = file_get_contents($fakeJson);
//        $dataF = json_decode($jsonData, true);
        $data = $this->userService->getUsers();
        if(!isset($data['users']) || !is_array($data['users'])) {
            throw new \RuntimeException("Erreur : donnees invalides! ");
        }
        foreach ($data['users'] as &$user)
        {
            foreach($user['farms'] as &$farm)
            {
                foreach($farm['lands'] as &$land)
                {
                    $latitude = $land['centerX'];
                    $longitude = $land['centerY'];
                    try{
                        $response = $this->client->request('GET', $this->buildWeatherApiUrl($latitude, $longitude));
                        if ($response->getStatusCode() !== 200) {
                            throw new \Exception("Erreur lors de la recuperation des donnees meteo : " . $response->getContent(false));
                        }
                        $land['weather'] = $response->toArray();
                    }catch (\Exception $exception){
                        $land['weather'] = ['error' => $exception->getMessage()];
                    }
                }
            }
        }
        return $data;
    }
}