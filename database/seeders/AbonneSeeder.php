<?php

namespace Database\Seeders;

use App\Models\Abonne;
use App\Models\Facture;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AbonneSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Abonne::factory()
            ->count(20)
            ->create()
            ->each(function (Abonne $abonne) {
                Facture::factory()
                    ->count(rand(1, 4))
                    ->create(['abonneId' => $abonne->id]);
            });
    }
}
