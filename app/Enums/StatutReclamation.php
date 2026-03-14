<?php

namespace App\Enums;

enum StatutReclamation: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case EN_COURS = 'EN_COURS';
    case APPROUVEE = 'APPROUVEE';
    case REJETTEE = 'REJETTEE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::EN_COURS => 'En cours',
            self::APPROUVEE => 'Approuvée',
            self::REJETTEE => 'Rejetée',
        };
    }
}
