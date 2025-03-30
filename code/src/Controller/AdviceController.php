<?php

namespace App\Controller;

use App\Entity\Advice;
use App\Entity\Land;
use App\Entity\LandPlants;
use App\Entity\Plants;
use App\Interface\RedAlertServiceInterface;
use App\Interface\TranslationServiceInterface;
use App\Interface\TTSServiceInterface;
use App\Repository\AdviceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api/advice", name:"api_advice_")]
class AdviceController extends AbstractController{
    
    private $repo;
    private $em;
    private $translator;
    private $tts;
    private $RedAlertService;

    public function __construct(
                AdviceRepository $repo_ ,
                EntityManagerInterface $em_, 
                TranslationServiceInterface $translator , 
                TTSServiceInterface $tts , 
                RedAlertServiceInterface $RedAlertService) 
        {
        $this->repo = $repo_;
        $this->em = $em_;
        $this->translator = $translator;
        $this->tts = $tts;
        $this->RedAlertService = $RedAlertService;
    }


    // Get all plants
    #[Route("", methods: ["GET"])]
    public function index(): JsonResponse
    {
        $advices = $this->repo->findAll();
        return $this->json($advices);
    }

    // READ (GET SINGLE Advice)
    #[Route("/{id}", methods: ["GET"])]
    public function getOne(int $id): JsonResponse
    {
        $advice = $this->em->getRepository(Advice::class)->find($id);
        
        if (!$advice) {
            return new JsonResponse(['error' => 'Advice not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($advice);
    }


    // Create a new advive
    #[Route("", methods: ["POST"])]
    public function create(Request $request): JsonResponse
    {

        // dd($request->getContentTypeFormat());
        // // Ensure request is JSON or jsonId for postman (keep json after hosting)
        // if ($request->getContentTypeFormat() !== 'jsonId') {
        //     return new JsonResponse(['error' => 'Unsupported Media Type'], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        // }

        $data = json_decode($request->getContent(), true);

        $advice = new Advice();
        
        //? setting advice's land
        $land = $this->em->getRepository(Land::class)->find($data['land_id']);
        if (!$land) {
            return new JsonResponse(['error' => 'Invalid land_id'], Response::HTTP_BAD_REQUEST);
        }

        //? setting advice's plant
        $plant = $this->em->getRepository(Plants::class)->find($data['plant_id']);
        if (!$plant) {
            return new JsonResponse(['error' => 'Invalid plant_id'], Response::HTTP_BAD_REQUEST);
        }

        //? Associate the LandPlant entity with the Advice entity
        $advice->setLand($land);
        $advice->setPlant($plant);
        $advice->setMinTempC($data['min_temp_c']);
        $advice->setMaxTempC($data['max_temp_c']);
        
        
        //? new weather attributes
        $advice->setMinHumidity($data['min_humidity']);
        $advice->setMaxHumidity($data['max_humidity']);
        $advice->setMinPrecipitation($data['min_precipitation']);
        $advice->setMaxPrecipitation($data['max_precipitation']);
        $advice->setMinWindSpeed($data['min_wind_speed']);
        $advice->setMaxWindSpeed($data['max_wind_speed']);

        //! Bug !!!!!!!!!!!!!! ? !!!!!!!!!!!!!!!! =======================================
        // if($this->RedAlertService->checkRedAlert($advice , $currentTemp ? )){
        //     $advice->setRedAlert(true);
        // }

        if (!empty($data['advice_text_en'])) {
            
            //? translation
            $translatedDarija = $this->translator->translateToDarija($data['advice_text_en']);
            $translatedFrench = $this->translator->translateToFrench($data['advice_text_en']);
            
            $advice->setAdviceTextEn($data['advice_text_en']);
            $advice->setAdviceTextAr($translatedDarija);
            $advice->setAdviceTextFr($translatedFrench);


            //? TTSing here
            $AudioPathAr = $this->tts->getAudio($translatedDarija , 'ar');
            $AudioPathFr = $this->tts->getAudio($translatedFrench , 'fr');
            $AudioPathEn = $this->tts->getAudio($data['advice_text_en'] , 'en');
            
            $advice->setAudioPathAr($AudioPathAr);
            $advice->setAudioPathFr($AudioPathFr);
            $advice->setAudioPathEn($AudioPathEn);
        }


        //! Convert string to DateTimeImmutable
        $createdAt = isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : new \DateTimeImmutable();
        // dd($createdAt);
        $advice->setCreatedAt($createdAt);  

        $this->em->persist($advice);
        $this->em->flush();

        return $this->json([
            'id' => $advice->getId(),
            'land_id' => $advice->getLand()->getId(),
            'plant_id' => $advice->getPlant()->getId(),
            'advice_text_en' => $advice->getAdviceTextEn(),
            'advice_text_fr' => $advice->getAdviceTextFr(),
            'advice_text_ar' => $advice->getAdviceTextAr(),
            'AudioPathAr' => $advice->getAudioPathAr(),
            'AudioPathFr' => $advice->getAudioPathFr(),
            'AudioPathEn' => $advice->getAudioPathEn(),
            'RedAlert'=> $advice->isRedAlert(),
            'created_at' => $advice->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }



    // UPDATE Advice
    #[Route("/{id}", methods: ["PUT"])]
    public function update(int $id, Request $request): JsonResponse
    {
        $advice = $this->repo->find($id);        
        if (!$advice) {
            return new JsonResponse(['error' => 'Advice not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['land_plant_id'])) {
            $advice->setUserPlantId($data['land_plant_id']);
        }

        if (isset($data['advice_text_en'])) {
            $advice->setAdviceTextEn($data['advice_text_en']);
    
            //? Translate to Moroccan Darija and French
            $translatedDarija = $this->translator->translateToDarija($data['advice_text_en']);
            $translatedFrench = $this->translator->translateToFrench($data['advice_text_en']);

            //? TTSing here
            $AudioPathAr = $this->tts->getAudio($translatedDarija , 'ar');
            $AudioPathFr = $this->tts->getAudio($translatedDarija , 'fr');
            $AudioPathEn = $this->tts->getAudio($translatedDarija , 'en');
            
            $advice->setAudioPathAr($AudioPathAr);
            $advice->setAudioPathFr($AudioPathFr);
            $advice->setAudioPathEn($AudioPathEn);
    
            $advice->setAdviceTextAr($translatedDarija);
            $advice->setAdviceTextFr($translatedFrench);
        }

    //im keep this to allow manual modification without needing translation service
    if (isset($data['advice_text_ar'])) {
        $advice->setAdviceTextAr($data['advice_text_ar']);
    }

    if (isset($data['created_at'])) {
        $advice->setCreatedAt(new \DateTimeImmutable($data['created_at']));
    }
        
        if (isset($data['created_at'])) {
            $advice->setCreatedAt(new \DateTimeImmutable($data['created_at']));
        }

        $this->em->flush();

        return $this->json($advice);
    }



    // DELETE Advice
    #[Route("/{id}", methods: ["DELETE"])]
    public function delete(int $id): JsonResponse
    {
        $advice = $this->repo->find($id);

        if (!$advice) {
            return new JsonResponse(['error' => 'Advice not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($advice);
        $this->em->flush();

        return new JsonResponse(['message' => 'Advice deleted successfully'], Response::HTTP_NO_CONTENT);
    }

}
