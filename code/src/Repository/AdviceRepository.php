<?php

namespace App\Repository;

use App\Entity\Advice;
use App\Service\RedAlertService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Advice>
 */
class AdviceRepository extends ServiceEntityRepository
{

    private RedAlertService $ra;
    
    public function __construct(ManagerRegistry $registry , RedAlertService $ra)
    {   
        $this->ra = $ra;
        parent::__construct($registry, Advice::class);
    }

    public function findByTemperatureRange(int $LandPlantId, float $currentTemp): array
    {
        $qb = $this->createQueryBuilder('a')
        ->join('a.landPlant', 'lp') // Assuming 'a.LandPlant' is the property of the Advice entity
        ->where('lp.id = :LandPlantId') // Comparing the ID of the LandPlant entity
        ->andWhere(':currentTemp BETWEEN a.min_temp_C AND a.max_temp_C')
        ->setParameter('LandPlantId', $LandPlantId) // Pass the ID of the LandPlant
        ->setParameter('currentTemp', $currentTemp); // Pass the temperature

        $advices = $qb->getQuery()->getResult();

        //! verify if current temp is in the safe range of temp 
        foreach ($advices as $advice) {
            if ($this->ra->checkRedAlert($advice)) {
                $advice->setRedAlert(true);
            }
        }

        return $advices;
    }


//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Advice
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
