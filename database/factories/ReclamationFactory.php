<?php

namespace Database\Factories;

use App\Enums\StatutReclamation;
use App\Models\Facture;
use App\Models\Reclamation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReclamationFactory extends Factory
{
    protected $model = Reclamation::class;

    public function definition(): array
    {
        $facture = Facture::inRandomOrder()->first() ?? Facture::factory()->create();

        $statut = $this->faker->randomElement(StatutReclamation::values());
        $hasResponse = in_array($statut, [StatutReclamation::APPROUVEE->value, StatutReclamation::REJETTEE->value], true);

        return [
            'factureId' => $facture->id,
            'contenu' => $this->faker->paragraph(2),
            'statut' => $statut,
            'reponse' => $hasResponse ? $this->faker->sentence() : null,
            'dateReponse' => $hasResponse ? $this->faker->dateTimeBetween('-30 days', 'now') : null,
        ];
    }
}
