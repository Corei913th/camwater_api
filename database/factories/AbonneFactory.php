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
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'quartier' => $this->faker->streetName(),
            'ville' => $this->faker->city(),
            'numeroCompteur' => $this->faker->unique()->numerify('CPT####'),
            'typeAbonnement' => $this->faker->randomElement(TypeAbonnement::values()),
        ];
    }
}
