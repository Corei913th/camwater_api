<?php

namespace Database\Factories;

use App\Enums\StatutFacture;
use App\Models\Abonne;
use App\Models\Facture;
use App\Services\CalculateurFacture;
use Illuminate\Database\Eloquent\Factories\Factory;

class FactureFactory extends Factory
{
    protected $model = Facture::class;

    public function definition(): array
    {
        $abonne = Abonne::inRandomOrder()->first() ?? Abonne::factory()->create();
        $consommation = $this->faker->numberBetween(1, 80);
        $montantTotal = (new CalculateurFacture)->calculerMontant($consommation, $abonne->typeAbonnement);

        return [
            'abonneId' => $abonne->id,
            'consommation' => $consommation,
            'dateEmission' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'statut' => $this->faker->randomElement(StatutFacture::values()),
            'montantTotal' => $montantTotal,
        ];
    }
}
