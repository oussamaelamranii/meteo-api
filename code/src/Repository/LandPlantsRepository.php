<?php

namespace App\Repository;


use App\Entity\LandPlants;
use App\Entity\UserPlants;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<UserPlants>
 */
class LandPlantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LandPlants::class);
    }

    public function findPlantsByLand(int $landId): array
    {
        return $this->createQueryBuilder('fp')
            ->andWhere('fp.land_id = :landId')
            ->setParameter('landId', $landId)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return UserPlants[] Returns an array of UserPlants objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserPlants
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
