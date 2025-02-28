<?php

namespace App\Controller;

use App\Entity\Advice;
use App\Service\TranslationService;
use App\Repository\AdviceRepository;
use App\Service\TTSService;
use Doctrine\ORM\EntityManager;
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

    public function __construct(AdviceRepository $repo_ , EntityManagerInterface $em_, TranslationService $translator , TTSService $tts) {
        $this->repo = $repo_;
        $this->em = $em_;
        $this->translator = $translator;
        $this->tts = $tts;
    }

    // Get all plants
    #[Route("", methods: ["GET"])]
    public function index(): JsonResponse
    {
        $advices = $this->repo->findAll();

        $data = [];
        foreach ($advices as $ad) {
            $data[] = [
                'id' => $ad->getId(),
                'user_plant_id' => $ad->getUserPlantId(),
                'advice_text_en' => $ad->getAdviceTextEn(),
                'advice_text_fr' => $ad->getAdviceTextFr(),
                'advice_text_ar' => $ad->getAdviceTextAr(),
                'CreatedAt' => $ad->getCreatedAt(),
            ];
        }

        return $this->json($data);
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

        // Ensure request is JSON
        if ($request->getContentTypeFormat() !== 'json') {
            return new JsonResponse(['error' => 'Unsupported Media Type'], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        $data = json_decode($request->getContent(), true);

        $advice = new Advice();
        $advice->setUserPlantId($data['user_plant_id']);

        if (!empty($data['advice_text_en'])) {
            
            //? translation
            $translatedDarija = $this->translator->translateToDarija($data['advice_text_en']);
            $translatedFrench = $this->translator->translateToFrench($data['advice_text_en']);

            //? TTSing here
            $AudioPath = $this->tts->getAudio($translatedDarija);
            
            $advice->setAudioPath($AudioPath);

            $advice->setAdviceTextEn($data['advice_text_en'] ?? null);
            $advice->setAdviceTextAr($translatedDarija);
            $advice->setAdviceTextFr($translatedFrench);
        }
        // $advice->setAdviceTextFr($data['advice_text_fr'] ?? null);
        // $advice->setAdviceTextAr($data['advice_text_ar'] ?? null);


        // Convert string to DateTimeImmutable
        $createdAt = isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : new \DateTimeImmutable();
        $advice->setCreatedAt($createdAt);

        $this->em->persist($advice);
        $this->em->flush();

        return $this->json([
            'id' => $advice->getId(),
            'user_plant_id' => $advice->getUserPlantId(),
            'advice_text_en' => $advice->getAdviceTextEn(),
            'advice_text_fr' => $advice->getAdviceTextFr(),
            'advice_text_ar' => $advice->getAdviceTextAr(),
            'AudioPath' => $advice->getAudioPath(),
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

        if (isset($data['user_plant_id'])) {
            $advice->setUserPlantId($data['user_plant_id']);
        }

        if (isset($data['advice_text_en'])) {
            $advice->setAdviceTextEn($data['advice_text_en']);
    
            //? Translate to Moroccan Darija and French
            $translatedDarija = $this->translator->translateToDarija($data['advice_text_en']);
            $translatedFrench = $this->translator->translateToFrench($data['advice_text_en']);

            //? TTSing here
            $AudioPath = $this->tts->getAudio($translatedDarija);
            
            $advice->setAudioPath($AudioPath);
    
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
