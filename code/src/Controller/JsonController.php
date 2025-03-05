<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class JsonController extends AbstractController

{
    #[Route('/json-test', name: 'json_test')]
    public function testJson(): JsonResponse
    {
        $filePath = '..\FakeJson.json';

        if (!file_exists($filePath)) {
            return new JsonResponse(['error' => 'File not found'], 404);
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON format'], 400);
        }

        return new JsonResponse($data);
    }
}
