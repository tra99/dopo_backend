<?php

namespace App\Enums;

enum QuestionTypeEnum: string
{
    case TEXT = 'text';
    case RADIO = 'radio';
    case CHECKBOX = 'checkbox';

    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'text',
            self::RADIO => 'radio',
            self::CHECKBOX => 'checkbox',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
