<?php

namespace App\Service;

use App\Interface\TTSServiceInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TTSService implements TTSServiceInterface
{
    private $client;
    private $apiKey;
    private $apiBaseUrl;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = $_ENV['TTS_API_KEY'];
        $this->apiBaseUrl = $_ENV['TTS_API_URL'];
    }

    public function getAudio(string $text , string $language): string
    {

            // Define voice IDs for each language
            $voiceMap = [
                'en' => 'noel',
                'fr' => 'raphael',
                'ar' => 'ismail'
            ];

            // Validate language choice
            if (!isset($voiceMap[$language])) {
                return new JsonResponse([
                    'error' => 'Language not supported',
                    'supported_languages' => array_keys($voiceMap),
                ], JsonResponse::HTTP_NOT_FOUND);
            }

        $response = $this->client->request('POST', "{$this->apiBaseUrl}/v1/audio/speech", [
            'json' => [
                'input' => "<speak>{$text}</speak>",
                'voice_id' => $voiceMap[$language],
                'model' => 'simba-multilingual',
                'audio_format' => "mp3",
            ],
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("Error: " . $response->getStatusCode() . " - " . $response->getContent(false));
        }

        $responseData = $response->toArray();
        $decodedAudio = base64_decode($responseData["audio_data"]);

        //? verifie si dossier existe
        $directory = __DIR__ . "/../../public/audio/";
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $uniqueFileName = uniqid('speech_', true) . '.mp3';
        $filePath = __DIR__ . "/../../public/audio/{$uniqueFileName}";

        file_put_contents($filePath, $decodedAudio);

        return "/audio/{$uniqueFileName}";

    }
}
