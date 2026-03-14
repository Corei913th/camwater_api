<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StatutReclamation;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factureId')->constrained('factures')->onDelete('cascade');
            $table->enum('statut', StatutReclamation::values());
            $table->text('contenu');
            $table->text('reponse')->nullable();
            $table->date('dateReponse')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamations');
    }
};