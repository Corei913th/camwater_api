<?php

use App\Enums\TypeAbonnement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abonnes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('quartier');
            $table->string('ville');
            $table->string('numeroCompteur')->unique();
            $table->enum('typeAbonnement', TypeAbonnement::values())
                ->default(TypeAbonnement::DOMESTIQUE);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abonnes');
    }
};
