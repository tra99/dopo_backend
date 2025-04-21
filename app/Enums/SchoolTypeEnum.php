<?php

namespace App\Enums;

enum SchoolTypeEnum: string
{
    case PRIMARYSCHOOL = 'Primary school';
    case LOWER_SECONDARYSCHOOL = 'Lower Secondary School';
    case UPPER_SECONDARYSCHOOL_7_12 = 'Upper Secondary (7-12)';
    case UPPER_SECONDARY_SCHOOL_10_12 = 'Upper Secondary (10-12)';

    public function label(): string
    {
        return match ($this) {
            self::PRIMARYSCHOOL => 'Primary school',
            self::LOWER_SECONDARYSCHOOL => 'Lower Secondary School',
            self::UPPER_SECONDARYSCHOOL_7_12 => 'Upper Secondary (7-12)',
            self::UPPER_SECONDARYSCHOOL_10_12 => 'Upper Secondary (10-12)',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
