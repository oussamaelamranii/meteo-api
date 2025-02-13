<?php

namespace App\Enum;

enum SunlightRequirement: string
{
    case FULL_SUN = 'full sun';
    case PARTIAL_SHADE = 'partial shade';
    case SHADE = 'shade';

    public function label(): string
    {
        return match($this) {
            self::FULL_SUN => 'Full Sun',
            self::PARTIAL_SHADE => 'Partial Shade',
            self::SHADE => 'Shade',
        };
    }
}
