<?php

    namespace App\Interface;

    use App\Entity\Advice;


    interface RedAlertServiceInterface
    {
        public function checkRedAlert(Advice $advice, ?float $currentTemp, ?float $currentHumidity, ?float $currentWindSpeed, ?float $currentPrecipitation): bool;
        public function checkRedAlertWeekly(Advice $advice, ?float $currentTemp, ?float $currentWindSpeed, ?float $currentPrecipitation): bool;
    }
?>