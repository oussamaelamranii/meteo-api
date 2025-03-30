<?php

namespace App\Repository;

use App\Entity\Plants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plants>
 */
class PlantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plants::class);
    }

    public function findByLandId(int $landId): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.lands', 'l')
            ->where('l.id = :landId')
            ->setParameter('landId', $landId)
            ->getQuery()
            ->getResult();
    }

    public function findIdByName(string $name): ?int
    {
        return $this->createQueryBuilder('p')
            ->select('p.id')
            ->where('p.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getSingleScalarResult(); // Returns the ID directly or throws an exception
    }

//    /**
//     * @return Plants[] Returns an array of Plants objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Plants
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
