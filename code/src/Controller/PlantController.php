<?php

namespace App\Controller;

use App\Entity\Plants;
use App\Repository\PlantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/api/plants', name: 'api_plants_')]
final class PlantController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/all' , methods: ['GET'])]
    public function list(PlantsRepository $plantsRepository): JsonResponse
    {
        $plants = $plantsRepository->findAll();

        $data = array_map(fn($plant) => [
            'id' => $plant->getId(),
            'name' => $plant->getName(),
        ], $plants);

        return $this->json($data);
    }


    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Plants $plant): JsonResponse
    {
        return $this->json([
            'id' => $plant->getId(),
            'name' => $plant->getName(),
        ]);
    }


    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name']) || empty($data['name'])) {
            return $this->json(['error' => 'Name is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $plant = new Plants();
        $plant->setName($data['name']);

        $this->entityManager->persist($plant);
        $this->entityManager->flush();

        return $this->json(['message' => 'Plant created successfully'], JsonResponse::HTTP_CREATED);
    }


    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Plants $plant): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name']) || empty($data['name'])) {
            return $this->json(['error' => 'Name is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $plant->setName($data['name']);
        $this->entityManager->flush();

        return $this->json(['message' => 'Plant updated successfully']);
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Plants $plant): JsonResponse
    {
        $this->entityManager->remove($plant);
        $this->entityManager->flush();

        return $this->json(['message' => 'Plant deleted successfully']);
    }
}
