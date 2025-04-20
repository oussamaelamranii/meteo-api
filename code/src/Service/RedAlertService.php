<?php

    namespace App\Service;

    use App\Entity\Advice;
use App\Entity\LandPlants;
use App\Entity\Plants;
use App\Interface\RedAlertServiceInterface;
use Doctrine\ORM\EntityManagerInterface;


    class RedAlertService implements RedAlertServiceInterface
    {

        public function checkRedAlert(Advice $advice, ?float $currentTemp, ?float $currentHumidity, ?float $currentWindSpeed, ?float $currentPrecipitation): bool
        {
            if ($currentTemp === null || $currentHumidity === null || $currentWindSpeed === null || $currentPrecipitation === null) {
                return false;
            }
        
            $plant = $advice->getPlant();
            if (!$plant) {
                return false;
            }
        
            // Check temperature range
            $safeMinTemp = $plant->getSafeMinTempC();
            $safeMaxTemp = $plant->getSafeMaxTempC();
            $tempAlert = ($currentTemp < $safeMinTemp || $currentTemp > $safeMaxTemp);
        
            // Check humidity range
            $safeMinHumidity = $plant->getSafeMinHumidity();
            $safeMaxHumidity = $plant->getSafeMaxHumidity();
            $humidityAlert = ($currentHumidity < $safeMinHumidity || $currentHumidity > $safeMaxHumidity);
        
            // Check wind speed range
            $safeMinWindSpeed = $plant->getSafeMinWindSpeed();
            $safeMaxWindSpeed = $plant->getSafeMaxWindSpeed();
            $windSpeedAlert = ($currentWindSpeed < $safeMinWindSpeed || $currentWindSpeed > $safeMaxWindSpeed);
        
            // Check precipitation range
            $safeMinPrecipitation = $plant->getSafeMinPrecipitation();
            $safeMaxPrecipitation = $plant->getSafeMaxPrecipitation();
            $precipitationAlert = ($currentPrecipitation < $safeMinPrecipitation || $currentPrecipitation > $safeMaxPrecipitation);
        
            // If any condition is outside the safe range, return true (red alert)
            return $tempAlert || $humidityAlert || $windSpeedAlert || $precipitationAlert;
        }



        public function checkRedAlertWeekly(Advice $advice, ?float $currentTemp, ?float $currentWindSpeed, ?float $currentPrecipitation): bool
        {
            if ($currentTemp === null || $currentWindSpeed === null || $currentPrecipitation === null) {
                return false;
            }
        
            $plant = $advice->getPlant();
            if (!$plant) {
                return false;
            }
        
            // Check temperature range
            $safeMinTemp = $plant->getSafeMinTempC();
            $safeMaxTemp = $plant->getSafeMaxTempC();
            $tempAlert = ($currentTemp < $safeMinTemp || $currentTemp > $safeMaxTemp);
        
        
            // Check wind speed range
            $safeMinWindSpeed = $plant->getSafeMinWindSpeed();
            $safeMaxWindSpeed = $plant->getSafeMaxWindSpeed();
            $windSpeedAlert = ($currentWindSpeed < $safeMinWindSpeed || $currentWindSpeed > $safeMaxWindSpeed);
        
            // Check precipitation range
            $safeMinPrecipitation = $plant->getSafeMinPrecipitation();
            $safeMaxPrecipitation = $plant->getSafeMaxPrecipitation();
            $precipitationAlert = ($currentPrecipitation < $safeMinPrecipitation || $currentPrecipitation > $safeMaxPrecipitation);
        
            // If any condition is outside the safe range, return true (red alert)
            return $tempAlert || $windSpeedAlert || $precipitationAlert;
        }
    }

?>