<?php

namespace App\Enum;

enum SunlightRequirement: string
{
    case FULL_SUN = 'Full Sun';
    case PARTIAL_SHADE = 'Partial Shade';
    case SHADE = 'Shade';

    public function label(): string
    {
        return match($this) {
            self::FULL_SUN => 'Full Sun',
            self::PARTIAL_SHADE => 'Partial Shade',
            self::SHADE => 'Shade',
        };
    }
}
