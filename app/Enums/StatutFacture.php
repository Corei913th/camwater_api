<?php

namespace App\Enums;

enum StatutFacture: string
{
    case EMISE = 'EMISE';
    case PAYEE = 'PAYEE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::EMISE => 'Emise',
            self::PAYEE => 'Payée',
        };
    }
}
