<?php

namespace Database\Seeders;

use App\Models\Facture;
use App\Models\Reclamation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReclamationSeeder extends Seeder
{
  use WithoutModelEvents;

  public function run(): void
  {
    Facture::all()->each(function (Facture $facture) {
      if (rand(1, 100) <= 40) { // ~40% des factures ont une réclamation
        Reclamation::factory()
          ->count(rand(1, 2))
          ->create(['factureId' => $facture->id]);
      }
    });
  }
}
