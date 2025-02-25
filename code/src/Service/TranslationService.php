<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TranslationService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = $_ENV['OPENAI_API_KEY']; // Store API key in .env file
    }

    public function translateToDarija(string $text): string
    {
        $response = $this->client->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a translator that converts English sentences to Moroccan Darija.'],
                    ['role' => 'user', 'content' => "Translate this to Moroccan Darija: $text"]
                ]
            ]
        ]);

        $data = $response->toArray();
        return $data['choices'][0]['message']['content'] ?? 'Translation failed';
    }

    public function translateToFrench(string $text): string
    {
        $response = $this->client->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a translator that converts English sentences to French.'],
                    ['role' => 'user', 'content' => "Translate this to French: $text"]
                ]
            ]
        ]);

        $data = $response->toArray();
        return $data['choices'][0]['message']['content'] ?? 'Translation failed';
    }
}
