<?php

    namespace App\Service;

    use App\Entity\Advice;
use App\Entity\LandPlants;
use App\Entity\Plants;
use App\Interface\RedAlertServiceInterface;
use Doctrine\ORM\EntityManagerInterface;


    class RedAlertService implements RedAlertServiceInterface
    {
        //! =============== fix ===========
        public function checkRedAlert(Advice $advice , ?float $currentTemp): bool
        {

            if($currentTemp == null){
                return false;
            }

            $plant = $advice->getPlant();

            if (!$plant) {
                return false;
            }

            $safeMin = $plant->getSafeMinTempC();
            $safeMax = $plant->getSafeMaxTempC();

            // If the advice's temperature range falls outside the plant's safe range
            return ($currentTemp < $safeMin || $currentTemp > $safeMax);
        }
    }

?>