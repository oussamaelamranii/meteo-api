<?php

    namespace App\Interface;

    interface AdviceGenerationServiceInterface
    {
        public function GenerateSpecificAdvice(array $WeatherConditions , string $plant): string;
        public function GenerateGeneralAdvice(string $plant): string;
    }

?>