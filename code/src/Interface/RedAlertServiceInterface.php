<?php

    namespace App\Interface;

    use App\Entity\Advice;


    interface RedAlertServiceInterface
    {
        public function checkRedAlert(Advice $advice, float $currentTemp): bool;
    }
?>