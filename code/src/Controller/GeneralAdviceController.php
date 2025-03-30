<?php

namespace App\Controller;

use App\Entity\Plants;
use App\Entity\GeneralAdvice;
use App\Form\GeneralAdviceType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GeneralAdviceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/general-advice')]
final class GeneralAdviceController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private GeneralAdviceRepository $generalAdviceRepository;

    public function __construct(EntityManagerInterface $entityManager,GeneralAdviceRepository $generalAdviceRepository)
    {
        $this->entityManager = $entityManager;
        // $this->generalAdviceRepository = $generalAdviceRepository;
    }


    #[Route('/', methods: ['GET'])]
    public function getAll(GeneralAdviceRepository $repo): JsonResponse
    {
        $advices = $repo->findAll();
    
        $formattedAdvices = array_map(fn($advice) => $this->formatAdvice($advice), $advices);

        return $this->json($formattedAdvices);
    }


    #[Route('/{id}', methods: ['GET'])]
    public function getOne(GeneralAdvice $advice): JsonResponse
    {
        if (!$advice) {
            return new JsonResponse(['error' => 'Advice not found'], 404);
        }
        
        return $this->json($this->formatAdvice($advice));
    }


    #[Route('/', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) return new JsonResponse(['error' => 'Invalid JSON'], 400);

        $advice = new GeneralAdvice();
        $advice->setAdviceTextEn($data['advice_text_en']);
        $advice->setAdviceTextFr($data['advice_text_fr']);
        $advice->setAdviceTextAr($data['advice_text_ar']);
        $advice->setAudioPathEn($data['audio_path_en'] ?? null);
        $advice->setAudioPathFr($data['audio_path_fr'] ?? null);
        $advice->setAudioPathAr($data['audio_path_ar'] ?? null);
        $advice->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($advice);
        $this->entityManager->flush();

        return new JsonResponse($this->formatAdvice($advice), 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, GeneralAdvice $advice): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) return new JsonResponse(['error' => 'Invalid JSON'], 400);

        $advice->setAdviceTextEn($data['advice_text_en'] ?? $advice->getAdviceTextEn());
        $advice->setAdviceTextFr($data['advice_text_fr'] ?? $advice->getAdviceTextFr());
        $advice->setAdviceTextAr($data['advice_text_ar'] ?? $advice->getAdviceTextAr());
        $advice->setAudioPathEn($data['audio_path_en'] ?? $advice->getAudioPathEn());
        $advice->setAudioPathFr($data['audio_path_fr'] ?? $advice->getAudioPathFr());
        $advice->setAudioPathAr($data['audio_path_ar'] ?? $advice->getAudioPathAr());

        $this->entityManager->flush();

        return new JsonResponse($this->formatAdvice($advice));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(GeneralAdvice $advice): JsonResponse
    {
        $this->entityManager->remove($advice);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Advice deleted']);
    }

    private function formatAdvice(GeneralAdvice $advice): array
    {
        return [
            'id' => $advice->getId(),
            'plant' => $advice->getPlant()->getName(),
            'advice_text_en' => $advice->getAdviceTextEn(),
            'advice_text_fr' => $advice->getAdviceTextFr(),
            'advice_text_ar' => $advice->getAdviceTextAr(),
            'audio_path_en' => $advice->getAudioPathEn(),
            'audio_path_fr' => $advice->getAudioPathFr(),
            'audio_path_ar' => $advice->getAudioPathAr(),
            'created_at' => $advice->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }


    // #[Route('/{id}', name: 'api_general_advice_show', methods: ['GET'])]
    // public function show(GeneralAdvice $generalAdvice): JsonResponse
    // {
    //     return $this->json($generalAdvice, 200);
    // }


    // #[Route('/', name: 'api_general_advice_create', methods: ['POST'])]
    // public function create(Request $request): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);
    //     if (!$data || !isset($data['plant'], $data['advice_text_en'], $data['AudioPathEn'])) {
    //         return $this->json(['error' => 'Missing required fields'], 400);
    //     }

    //     $generalAdvice = new GeneralAdvice();
    //     $generalAdvice->setPlant($this->entityManager->getRepository(Plants::class)->find($data['plant']));
    //     $generalAdvice->setAdviceTextEn($data['advice_text_en']);
    //     $generalAdvice->setAdviceTextFr($data['advice_text_fr'] ?? null);
    //     $generalAdvice->setAdviceTextAr($data['advice_text_ar'] ?? null);
    //     $generalAdvice->setAudioPathEn($data['AudioPathEn']);
    //     $generalAdvice->setAudioPathFr($data['AudioPathFr'] ?? null);
    //     $generalAdvice->setAudioPathAr($data['AudioPathAr'] ?? null);

    //     $this->entityManager->persist($generalAdvice);
    //     $this->entityManager->flush();

    //     return $this->json($generalAdvice, 201, [], ['groups' => 'general_advice']);
    // }


    // #[Route('/{id}', name: 'api_general_advice_update', methods: ['PUT', 'PATCH'])]
    // public function update(Request $request, GeneralAdvice $generalAdvice): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);

    //     if (isset($data['plant'])) {
    //         $generalAdvice->setPlant($this->entityManager->getRepository(Plants::class)->find($data['plant']));
    //     }
    //     if (isset($data['advice_text_en'])) {
    //         $generalAdvice->setAdviceTextEn($data['advice_text_en']);
    //     }
    //     if (isset($data['advice_text_fr'])) {
    //         $generalAdvice->setAdviceTextFr($data['advice_text_fr']);
    //     }
    //     if (isset($data['advice_text_ar'])) {
    //         $generalAdvice->setAdviceTextAr($data['advice_text_ar']);
    //     }
    //     if (isset($data['AudioPathEn'])) {
    //         $generalAdvice->setAudioPathEn($data['AudioPathEn']);
    //     }
    //     if (isset($data['AudioPathFr'])) {
    //         $generalAdvice->setAudioPathFr($data['AudioPathFr']);
    //     }
    //     if (isset($data['AudioPathAr'])) {
    //         $generalAdvice->setAudioPathAr($data['AudioPathAr']);
    //     }

    //     $this->entityManager->flush();

    //     return $this->json($generalAdvice, 200, [], ['groups' => 'general_advice']);
    // }
}
