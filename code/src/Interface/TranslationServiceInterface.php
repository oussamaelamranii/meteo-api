<?php
    namespace App\Interface;

    interface TranslationServiceInterface
    {
        public function translateToDarija(string $text): string;
        public function translateToFrench(string $text): string;
    }
?>