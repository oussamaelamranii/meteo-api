<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TTSService
{
    private $client;
    private $apiKey;
    private $apiBaseUrl;
    private $voiceId;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = $_ENV['TTS_API_KEY'];
        $this->apiBaseUrl = $_ENV['TTS_API_URL'];
        $this->voiceId = "ismail";
    }

    public function getAudio(string $text): string
    {
        $response = $this->client->request('POST', "{$this->apiBaseUrl}/v1/audio/speech", [
            'json' => [
                'input' => "<speak>{$text}</speak>",
                'voice_id' => $this->voiceId,
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
