<?php

namespace App\Service;

use App\Interface\AdviceGenerationServiceInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdviceGenerationService implements AdviceGenerationServiceInterface
{
    private $client;
    private $apiKey;
    private CurrentWeatherService $currWeatherService;

    public function __construct(HttpClientInterface $client , CurrentWeatherService $currWeatherService)
    {
        $this->client = $client;
        $this->currWeatherService = $currWeatherService;
        $this->apiKey = $_ENV['OPENAI_API_KEY']; 
    }





    public function GenerateGeneralAdvice(string $plant): string
    {
        $prompt = "You are an expert agricultural advisor.
            Provide a farming advice on how to care for the plant **$plant** in generale,keep it short, clear and direct and in one sentence ";

        $response = $this->client->request('POST', $_ENV['OPENAI_API_URL'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a professional farmer providing plant care advice, Your response should be **short (3-5 sentences), clear, and free of unnecessary formatting like "\\n".**'],
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]
        ]);

        $data = $response->toArray();
        return $data['choices'][0]['message']['content'] ?? 'Generation failed';
    }


    public function GenerateSpecificAdvice(array $WeatherConditions , string $plant): string
    {

        $temp = $WeatherConditions['temperature'];
        $humidity = $WeatherConditions['humidity'];
        $precipitation = $WeatherConditions['precipitation'];
        $windSpeed = $WeatherConditions['wind_speed'];

        $prompt = "You are an expert agricultural advisor. Based on the following weather conditions:
            - Temperature: $temp °C
            - Humidity: $humidity %
            - Precipitation: $precipitation mm
            - Wind Speed: $windSpeed km/h    
            Provide a farming advice on how to care for the plant **$plant** in these conditions,keep it short, clear and direct ";

        $response = $this->client->request('POST', $_ENV['OPENAI_API_URL'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a professional farmer providing plant care advice, Your response should be **short (3-5 sentences), clear, and free of unnecessary formatting like "\\n".**'],
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]
        ]);

        $data = $response->toArray();
        return $data['choices'][0]['message']['content'] ?? 'Generation failed';
    }

    public function GenerateWeeklySpecificAdvice(array $WeatherConditions , string $plant): string
    {

        $temp = $WeatherConditions['temperature_max'];
        $precipitation = $WeatherConditions['precipitation_sum'];
        $windSpeed = $WeatherConditions['wind_speed_max'];

        $prompt = "You are an expert agricultural advisor. Based on the following weather conditions:
            - Temperature: $temp °C
            - Precipitation: $precipitation mm
            - Wind Speed: $windSpeed km/h    
            Provide a farming advice on how to care for the plant **$plant** in these conditions,keep it short, clear and direct ";

        $response = $this->client->request('POST', $_ENV['OPENAI_API_URL'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a professional farmer providing plant care advice, Your response should be **short (3-5 sentences), clear, and free of unnecessary formatting like "\\n".**'],
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]
        ]);

        $data = $response->toArray();
        // dump($data);
        return $data['choices'][0]['message']['content'] ?? 'Generation failed';
    }


    public function filterWeatherByUserFarmLand(array $weatherData, ?string $userId = null, ?string $farmId = null, ?string $landId = null): array
    {

        if (!isset($weatherData['Users'])) {
            return [];
        }

        foreach ($weatherData['Users'] as $user) {
            // Filter by user ID if provided
            if ($userId !== null && $user['UserId'] != $userId) {
                continue;
            }

            foreach ($user['Farms'] as $farm) {
                // Filter by farm ID if provided
                if ($farmId !== null && $farm['FarmId'] != $farmId) {
                    continue;
                }

                foreach ($farm['Lands'] as $land) {
                    // Filter by land ID if provided
                    if ($landId !== null && $land['LandId'] != $landId) {
                        continue;
                    }

                    if (!isset($land['Meteo'])) {
                        continue;
                    }

                    $meteoData = $land['Meteo'];

                    $currentTimeIndex = $this->currWeatherService->getCurrentTimeIndex($meteoData['hourly']);
                    // dd($currentTimeIndex);
                }
            }
        }
        
        return [
            'temperature' => $meteoData['hourly']['temperature_2m'][$currentTimeIndex] ?? null,
            'humidity' => $meteoData['hourly']['relative_humidity_2m'][$currentTimeIndex] ?? null,
            'precipitation' => $meteoData['hourly']['precipitation'][$currentTimeIndex] ?? null,
            'wind_speed' => $meteoData['hourly']['wind_speed_10m'][$currentTimeIndex] ?? null,
        ];
    }




    public function filterWeatherByUserFarmLandWeekly(array $weatherData, ?string $userId = null, ?string $farmId = null, ?string $landId = null): array
    {

        if (!isset($weatherData['Users'])) {
            return [];
        }

        foreach ($weatherData['Users'] as $user) {
            // Filter by user ID if provided
            if ($userId !== null && $user['UserId'] != $userId) {
                continue;
            }

            foreach ($user['Farms'] as $farm) {
                // Filter by farm ID if provided
                if ($farmId !== null && $farm['FarmId'] != $farmId) {
                    continue;
                }

                foreach ($farm['Lands'] as $land) {
                    // Filter by land ID if provided
                    if ($landId !== null && $land['LandId'] != $landId) {
                        continue;
                    }

                    if (!isset($land['Meteo'])) {
                        continue;
                    }

                    $meteoData = $land['Meteo'];
                    $plantId = $land['PlantId'];
                }
            }
        }
        
        return $meteoData['daily'];
    }


}
