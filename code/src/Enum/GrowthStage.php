<?php

namespace App\Enum;

enum GrowthStage: string
{
    case SEEDLING = 'seedling';
    case VEGETATIVE = 'vegetative';
    case FLOWERING = 'flowering';
    case FRUITING = 'fruiting';

    public function label(): string
    {
        return match($this) {
            self::SEEDLING => 'Seedling',
            self::VEGETATIVE => 'Vegetative',
            self::FLOWERING => 'Flowering',
            self::FRUITING => 'Fruiting',
        };
    }
}