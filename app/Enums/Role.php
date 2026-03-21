<?php

namespace App\Enums;

enum Role: string
{
    case OPERATEUR = 'OPERATEUR';
    case ADMIN = 'ADMIN';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::OPERATEUR => 'Opérateur',
            self::ADMIN => 'Admin',
        };
    }
}
