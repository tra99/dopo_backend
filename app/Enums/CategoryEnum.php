<?php

namespace App\Enums;

enum CategoryEnum: string
{
    case CATEGORY = 'standard';
    case GROUP = 'group';
    case SOCHANAKOR = 'sochanakor';

    public function label(): string
    {
        return match ($this) {
            self::CATEGORY => 'standard',
            self::GROUP => 'group',
            self::SOCHANAKOR => 'sochanakor',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
