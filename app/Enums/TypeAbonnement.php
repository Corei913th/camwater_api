<?php

namespace App\Enums;

enum TypeAbonnement: string
{
    case PROFESSIONNEL = 'PROFESSIONNEL';
    case DOMESTIQUE = 'DOMESTIQUE';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PROFESSIONNEL => 'Professionnel',
            self::DOMESTIQUE => 'Domestique'
        };
    }
};


