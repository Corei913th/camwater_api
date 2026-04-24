<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Utilisateur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        Utilisateur::factory()->create([
            'email' => 'admin@camwaterpro.com',
            'role' => Role::ADMIN,
        ]);

        Utilisateur::factory()->create([
            'email' => 'operator@camwaterpro.com',
            'role' => Role::OPERATEUR,
        ]);

        $this->call([
            AbonneSeeder::class,
            ReclamationSeeder::class,
        ]);
    }
}
