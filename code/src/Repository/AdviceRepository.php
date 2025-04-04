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



    public function deleteOldAdvices(): void
    {
        // Get the current date and format it to 'YYYY-MM-DD'
        $currentDate = new \DateTime();
        $formattedDate = $currentDate->format('Y-m-d'); // '2025-04-02'

        // Execute the query to delete old advices
        $qb = $this->createQueryBuilder('a');
        $qb->delete()
            ->where('a.advice_date < :today')
            ->setParameter('today', $formattedDate);

        $qb->getQuery()->execute();
    }



    public function findByWeatherConditions(
        int $landId, 
        ?float $currentTemp, 
        ?float $currentHumidity, 
        ?float $currentPrecipitation, 
        ?float $currentWindSpeed
    ): array {
        
        $currentDate = new \DateTime();
        $formattedDate = $currentDate->format('Y-m-d');
        // $formattedDate = "2025-04-02";
    
        // Create query builder
        $qb = $this->createQueryBuilder('a')
            ->where('a.land = :landId')
            ->andWhere("DATE(a.created_at) = :today") // Check if advice date is today's date
            ->setParameter('today', $formattedDate);
    
        // Apply weather condition filters if available
        if ($currentTemp !== null) {
            $qb->andWhere(':currentTemp BETWEEN a.min_temp_C AND a.max_temp_C')
                ->setParameter('currentTemp', $currentTemp);
        }
    
        if ($currentHumidity !== null) {
            $qb->andWhere(':currentHumidity BETWEEN a.min_humidity AND a.max_humidity')
                ->setParameter('currentHumidity', $currentHumidity);
        }
    
        if ($currentPrecipitation !== null) {
            $qb->andWhere(':currentPrecipitation BETWEEN a.min_precipitation AND a.max_precipitation')
                ->setParameter('currentPrecipitation', $currentPrecipitation);
        }
    
        if ($currentWindSpeed !== null) {
            $qb->andWhere(':currentWindSpeed BETWEEN a.min_wind_speed AND a.max_wind_speed')
                ->setParameter('currentWindSpeed', $currentWindSpeed);
        }
    
        // Set the landId parameter
        $qb->setParameter('landId', $landId);
    
        // Execute the query and return results
        return $qb->getQuery()->getResult();
    }


    public function findByWeatherDates(
        int $landId, 
        string $adviceDate 
    ): array {
        // Create query builder
        $qb = $this->createQueryBuilder('a')
            ->where('a.land = :landId')
            ->andWhere('a.advice_date = :today') // Check if advice date is today's date
            ->setParameter('today', $adviceDate)
            ->setParameter('landId', $landId);
    
        // Execute the query and return results
        return $qb->getQuery()->getResult();
    }
    

    // public function findByTemperatureRange(int $landId, int $plantId, float $currentTemp): array
    // {
    //     $qb = $this->createQueryBuilder('a')
    //     ->join('a.land', 'l')
    //     ->join('a.plant', 'p')
    //     ->where('l.id = :landId')
    //     ->andWhere('p.id = :plantId')
    //     ->andWhere(':currentTemp BETWEEN a.min_temp_C AND a.max_temp_C')
    //     ->setParameter('landId', $landId)
    //     ->setParameter('plantId', $plantId)
    //     ->setParameter('currentTemp', $currentTemp);

    //     $advices = $qb->getQuery()->getResult();

    //     //! verify if current temp is in the safe range of temp 
    //     foreach ($advices as $advice) {
    //         if ($this->ra->checkRedAlert($advice,$currentTemp)) {
    //             $advice->setRedAlert(true);
    //         }
    //     }

    //     return $advices;
    // }


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
