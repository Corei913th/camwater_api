<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\Role;
use App\Models\Utilisateur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AbonneSeeder;
use Database\Seeders\ReclamationSeeder;

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
            'email' => 'admin@example.com',
            'role' => Role::ADMIN,
        ]);

        Utilisateur::factory()->create([
            'email' => 'operator@example.com',
            'role' => Role::OPERATEUR,
        ]);


        $this->call([
            AbonneSeeder::class,
            ReclamationSeeder::class,
        ]);
    }
}
