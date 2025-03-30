<?php

namespace App\Service;

use App\Entity\Land;
use App\Entity\Advice;
use App\Entity\GeneralAdvice;
use App\Entity\Plants;
use App\Repository\PlantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Interface\RedAlertServiceInterface;
use App\Interface\TranslationServiceInterface;
use App\Interface\TTSServiceInterface;


class AdviceService {

    private EntityManagerInterface $em;
    private PlantsRepository $PlantRepo;
    private TranslationServiceInterface $translator;
    private TTSServiceInterface $tts;

    public function __construct(
        EntityManagerInterface $em , 
        PlantsRepository $PlantRepo,
        TranslationServiceInterface $translator , 
        TTSServiceInterface $tts , 
        )
    {
        $this->em = $em;
        $this->PlantRepo = $PlantRepo;
        $this->translator = $translator;
        $this->tts = $tts;
    }


    public function InsertGeneralAdvice(string $plantName , string $AdviceText): JsonResponse
    {

        $GeneralAdvice = new GeneralAdvice();

        //* translate and tts
        if (!empty($AdviceText)) {
    
            //? translation
            $translatedDarija = $this->translator->translateToDarija($AdviceText);
            $translatedFrench = $this->translator->translateToFrench($AdviceText);
            
            $GeneralAdvice->setAdviceTextEn($AdviceText);
            $GeneralAdvice->setAdviceTextAr($translatedDarija);
            $GeneralAdvice->setAdviceTextFr($translatedFrench);


            //? TTSing here
            $AudioPathFr = $this->tts->getAudio($translatedFrench , 'fr');
            $AudioPathAr = $this->tts->getAudio($translatedDarija , 'ar');
            $AudioPathEn = $this->tts->getAudio($AdviceText , 'en');
            
            $GeneralAdvice->setAudioPathAr($AudioPathAr);
            $GeneralAdvice->setAudioPathFr($AudioPathFr);
            $GeneralAdvice->setAudioPathEn($AudioPathEn);
        }

        //? setting advice's plant
        $plantId = $this->PlantRepo->findIdByName($plantName);        
        $plant = $this->em->getRepository(Plants::class)->find($plantId);

        if (!$plantId) {
            return new JsonResponse(['error' => 'Invalid plant_id'], Response::HTTP_BAD_REQUEST);
        }

        //? Associate the plant
        $GeneralAdvice->setPlant($plant);


        //? Set CreatedAt
        $createdAt = isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : new \DateTimeImmutable();
        $GeneralAdvice->setCreatedAt($createdAt);  


        //! saving 
        $this->em->persist($GeneralAdvice);
        $this->em->flush();


        return new JsonResponse($GeneralAdvice, Response::HTTP_OK);
    }



    public function InsertSpecificAdvice(int $landId, string $plantName, array $WeatherConditions , string $AdviceText): JsonResponse
    {
        
        $advice = new Advice();

        //* translate and tts
        if (!empty($AdviceText)) {
            
            //? translation
            $translatedDarija = $this->translator->translateToDarija($AdviceText);
            $translatedFrench = $this->translator->translateToFrench($AdviceText);
            
            $advice->setAdviceTextEn($AdviceText);
            $advice->setAdviceTextAr($translatedDarija);
            $advice->setAdviceTextFr($translatedFrench);


            //? TTSing here
            $AudioPathFr = $this->tts->getAudio($translatedFrench , 'fr');
            $AudioPathAr = $this->tts->getAudio($translatedDarija , 'ar');
            $AudioPathEn = $this->tts->getAudio($AdviceText , 'en');
            
            $advice->setAudioPathAr($AudioPathAr);
            $advice->setAudioPathFr($AudioPathFr);
            $advice->setAudioPathEn($AudioPathEn);
        }


        //? Set CreatedAt
        $createdAt = isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : new \DateTimeImmutable();
        $advice->setCreatedAt($createdAt);  
        
        //? setting advice's land
        $land = $this->em->getRepository(Land::class)->find($landId);
        if (!$land) {
            return new JsonResponse(['error' => 'Invalid land_id'], Response::HTTP_BAD_REQUEST);
        }

        //? setting advice's plant
        $plantId = $this->PlantRepo->findIdByName($plantName);        
        $plant = $this->em->getRepository(Plants::class)->find($plantId);

        if (!$plantId) {
            return new JsonResponse(['error' => 'Invalid plant_id'], Response::HTTP_BAD_REQUEST);
        }

        //? Associate the LandPlant entity with the Advice entity
        $advice->setLand($land);
        $advice->setPlant($plant);

        //* //////////////////////
        $temp = $WeatherConditions['temperature'];
        $humidity = $WeatherConditions['humidity'];
        $precipitation = $WeatherConditions['precipitation'];
        $windSpeed = $WeatherConditions['wind_speed'];


        $advice->setMinTempC($temp-3);
        $advice->setMaxTempC($temp+3);

        $advice->setMinHumidity($humidity-3);
        $advice->setMaxHumidity($humidity+3);

        $advice->setMinPrecipitation($precipitation-3);
        $advice->setMaxPrecipitation($precipitation+3);

        $advice->setMinWindSpeed($windSpeed-3);
        $advice->setMaxWindSpeed($windSpeed+3);

        //! saving 
        $this->em->persist($advice);
        $this->em->flush();


        return new JsonResponse($advice, Response::HTTP_OK);
    }

}
