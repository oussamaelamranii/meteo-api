<?php

namespace App\Controller;

use App\Entity\Land;
use App\Entity\Advice;
use App\Entity\Plants;
use App\Service\AdviceGenerationService;
use App\Service\AdviceService;
use App\Service\CurrentWeatherService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[Route('/api/generate_advice')]
final class AdviceGenerationController extends AbstractController
{

    private AdviceGenerationService $adviceGeneration;
    private AdviceService $AdviceService;
    private EntityManagerInterface $em;
    private CurrentWeatherService $CurrAdvice;

    public function __construct(AdviceGenerationService $adviceGeneration , EntityManagerInterface $em , AdviceService $AdviceService , CurrentWeatherService $CurrAdvice) {
        $this->adviceGeneration = $adviceGeneration;
        $this->AdviceService = $AdviceService;
        $this->em = $em;
        $this->CurrAdvice = $CurrAdvice;
    }


    #[Route('/specific_advice/{userId}/{farmId}/{landId}/{plant}', methods:['GET'])]
    public function GenerateSpecificAdvice(int $userId , int $farmId , int $landId , string $plant): JsonResponse
    {
        // * //////////////// this is the code that should be used  ! /////////////////////
            // Fetch weather data for the user
            $weatherData = $this->CurrAdvice->getWeatherAllCache();           

            if (!$weatherData) {
                return new JsonResponse(['error' => 'Failed to fetch weather data'], JsonResponse::HTTP_BAD_REQUEST);
            }        
        // * //////////////////////////////////////////////////////////

        // //! //////////////// Just for Testing ! /////////////////////
            // $jsonFile = $this->getParameter('kernel.project_dir') . '/var/CacheJson.json';
            // $weatherData = json_decode(file_get_contents($jsonFile), true);
            // dd($weatherData);
        // //! //////////////// Just for Testing ! /////////////////////

        $userWeatherConditions = $this->adviceGeneration->filterWeatherByUserFarmLand($weatherData , $userId , $farmId , $landId);

        // dd($userWeatherConditions);

        $GeneratedAdvice = $this->adviceGeneration->GenerateSpecificAdvice($userWeatherConditions , $plant);

        //* insert generated advice into advice table
        $this->AdviceService->InsertSpecificAdvice($landId , $plant , $userWeatherConditions , $GeneratedAdvice);


        return $this->json([
            'Plant'=> $plant,
            'Land'=> $landId,
            'User'=> $userId,
            'WeatherConditions'=> $userWeatherConditions,
            'GeneratedAdvice'=> $GeneratedAdvice
        ]);
    }



    #[Route('/general_advice/{plant}', methods:['GET'])]
    public function GenerateGeneralAdvice(string $plant): JsonResponse
    {
        $GeneratedAdvice = $this->adviceGeneration->GenerateGeneralAdvice($plant);

        $this->AdviceService->InsertGeneralAdvice($plant , $GeneratedAdvice);

        return $this->json([
            'Plant'=> $plant,
            'GeneratedAdvice'=> $GeneratedAdvice
        ]);
    }
}
