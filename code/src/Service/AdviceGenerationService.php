<?php

namespace App\Service;

use App\Interface\AdviceGenerationServiceInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class AdviceGenerationService implements AdviceGenerationServiceInterface
{
    private $client;
    private $apiKey;
    private CurrentWeatherService $currWeatherService;

    public function __construct(HttpClientInterface $client , CurrentWeatherService $currWeatherService)
    {
        $this->client = $client;
        $this->currWeatherService = $currWeatherService;
    }


    public function GenerateWideRangeAdvice(string $description): string
    {

        $prompt = "You are an expert agricultural advisor. A weather change is going to hit the land: '$description'. Based on this change, provide a short and direct farming advice for the farmer, like 'A high-speed wind will hit your land, better cover them.' Make sure the advice is actionable and to the point, and always start by saying 'Warning : A {put the weather change from description here}' then give the advice";
        
        // dd($_ENV['OPENAI_API_KEY']);

        try{

        $response = $this->client->request('POST', $_ENV['OPENAI_API_URL'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $_ENV['OPENAI_API_KEY'],
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a professional agricultural advisor. Provide short, clear, and actionable advice based on the weather change described.'],
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]
        ]);

        $data = $response->toArray();
        return $data['choices'][0]['message']['content'] ?? 'Generation failed';

    } catch (ClientExceptionInterface $e) {
        // Parse the error response
        $errorContent = $e->getResponse()->getContent(false); // don't throw again
        $errorData = json_decode($errorContent, true);
        return $errorData['error']['message'] ?? 'API Error';
    }
    }



    public function GenerateGeneralAdvice(string $plant): string
    {
        $prompt = "You are an expert agricultural advisor.
            Provide a farming advice on how to care for the plant **$plant** in generale,keep it short, clear and direct and in one sentence ";
        
            // dd($_ENV['OPENAI_API_KEY']);

        $response = $this->client->request('POST', $_ENV['OPENAI_API_URL'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $_ENV['OPENAI_API_KEY'],
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
                'Authorization' => 'Bearer ' . $_ENV['OPENAI_API_KEY'],
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


    public function GenerateWeeklySpecificAdvice(array $weeklyWeather, string $plant): string
    {
        $prompt = "You are an expert agricultural advisor. I will give you the weather forecast for the next 7 days. 
    Each day has its temperature, precipitation, and wind speed. For each day, provide a short, clear, and direct advice (3-5 sentences max) on how to care for the plant **$plant**. 
    Separate each day's advice with '|||'.

    ";

        foreach ($weeklyWeather as $i => $day) {
            $prompt .= "Day " . ($i + 1) . ":
            - Temperature: {$day['temperature_max']} °C
            - Precipitation: {$day['precipitation_sum']} mm
            - Wind Speed: {$day['wind_speed_max']} km/h
    ";
        }

        $response = $this->client->request('POST', $_ENV['OPENAI_API_URL'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $_ENV['OPENAI_API_KEY'],
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a professional farmer giving plant care advice. Reply with 7 short advices separated by "|||".'],
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]
        ]);

        $data = $response->toArray();
        return $data['choices'][0]['message']['content'] ?? 'Generation failed';
    }



    //! public function GenerateWeeklySpecificAdvice(array $WeatherConditions , string $plant): string
    // {

    //     $temp = $WeatherConditions['temperature_max'];
    //     $precipitation = $WeatherConditions['precipitation_sum'];
    //     $windSpeed = $WeatherConditions['wind_speed_max'];

    //     $prompt = "You are an expert agricultural advisor. Based on the following weather conditions:
    //         - Temperature: $temp °C
    //         - Precipitation: $precipitation mm
    //         - Wind Speed: $windSpeed km/h    
    //         Provide a farming advice on how to care for the plant **$plant** in these conditions,keep it short, clear and direct ";

    //     $response = $this->client->request('POST', $_ENV['OPENAI_API_URL'], [
    //         'headers' => [
    //             'Authorization' => 'Bearer ' . $this->apiKey,
    //             'Content-Type' => 'application/json',
    //         ],
    //         'json' => [
    //             'model' => 'gpt-4o-mini',
    //             'messages' => [
    //                 ['role' => 'system', 'content' => 'You are a professional farmer providing plant care advice, Your response should be **short (3-5 sentences), clear, and free of unnecessary formatting like "\\n".**'],
    //                 ['role' => 'user', 'content' => $prompt]
    //             ]
    //         ]
    //     ]);

    //     $data = $response->toArray();
    //     // dump($data);
    //     return $data['choices'][0]['message']['content'] ?? 'Generation failed';
    // }


    public function filterWeatherByUserFarmLand(array $weatherData, ?string $userId = null, ?string $farmId = null, ?string $landId = null): array
    {

        if (!isset($weatherData['users'])) {
            return [];
        }

        foreach ($weatherData['users'] as $user) {
            // Filter by user ID if provided
            if ($userId !== null && $user['userId'] != $userId) {
                continue;
            }

            foreach ($user['farms'] as $farm) {
                // Filter by farm ID if provided
                if ($farmId !== null && $farm['farmId'] != $farmId) {
                    continue;
                }

                foreach ($farm['lands'] as $land) {
                    // Filter by land ID if provided
                    if ($landId !== null && $land['landId'] != $landId) {
                        continue;
                    }

                    if (!isset($land['weather'])) {
                        continue;
                    }

                    $meteoData = $land['weather'];

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

        if (!isset($weatherData['users'])) {
            return [];
        }

        foreach ($weatherData['users'] as $user) {
            // Filter by user ID if provided
            if ($userId !== null && $user['userId'] != $userId) {
                continue;
            }

            foreach ($user['farms'] as $farm) {
                // Filter by farm ID if provided
                if ($farmId !== null && $farm['farmId'] != $farmId) {
                    continue;
                }

                foreach ($farm['lands'] as $land) {
                    // Filter by land ID if provided
                    if ($landId !== null && $land['landId'] != $landId) {
                        continue;
                    }

                    if (!isset($land['weather'])) {
                        continue;
                    }

                    $meteoData = $land['weather'];
                    $plantId = $land['plantId'];
                }
            }
        }
        
        return $meteoData['daily'];
    }


}
