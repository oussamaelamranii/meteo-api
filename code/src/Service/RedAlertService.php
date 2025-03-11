<?php

    namespace App\Service;

    use App\Entity\Advice;
use App\Entity\LandPlants;
use App\Entity\Plants;
use App\Interface\RedAlertServiceInterface;
use Doctrine\ORM\EntityManagerInterface;


    class RedAlertService implements RedAlertServiceInterface
    {
        
        public function checkRedAlert(Advice $advice): bool
        {
            $plant = $advice->getLandPlant()->getPlant();

            if (!$plant) {
                return false;
            }

            $safeMin = $plant->getSafeMinTempC();
            $safeMax = $plant->getSafeMaxTempC();

            // If the advice's temperature range falls outside the plant's safe range
            return ($advice->getMaxTempC() > $safeMax || $advice->getMinTempC() < $safeMin);
        }
    }

?>