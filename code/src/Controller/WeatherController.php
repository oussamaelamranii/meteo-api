<?php

namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    private WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    #[Route('/weather', name: 'get_weather', methods: ['GET'])]
    public function getWeather(): JsonResponse
    {
        $weather = $this->weatherService->getWeather();
        if(empty($weather))
        {
            return new JsonResponse(['error' => 'Weather not found'], 404);
            //return new JsonResponse(['error' => 'Weather not found'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($weather);
    }

}