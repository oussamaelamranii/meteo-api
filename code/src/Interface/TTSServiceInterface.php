<?php

    namespace App\Interface;

    interface TTSServiceInterface
    {
        public function getAudio(string $text): string;
    }

?>