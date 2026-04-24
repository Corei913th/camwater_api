<?php

namespace Database\Factories;

use App\Enums\TypeAbonnement;
use App\Models\Abonne;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbonneFactory extends Factory
{
    protected $model = Abonne::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'quartier' => fake()->streetName(),
            'ville' => fake()->city(),
            'numeroCompteur' => fake()->unique()->numerify('CPT####'),
            'typeAbonnement' => fake()->randomElement(TypeAbonnement::values()),
        ];
    }
}
