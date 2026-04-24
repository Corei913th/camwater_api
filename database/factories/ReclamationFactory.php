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

        $statut = fake()->randomElement(StatutReclamation::values());
        $hasResponse = in_array($statut, [StatutReclamation::APPROUVEE->value, StatutReclamation::REJETTEE->value], true);

        return [
            'factureId' => $facture->id,
            'contenu' => fake()->paragraph(2),
            'statut' => $statut,
            'reponse' => $hasResponse ? fake()->sentence() : null,
            'dateReponse' => $hasResponse ? fake()->dateTimeBetween('-30 days', 'now') : null,
        ];
    }
}
