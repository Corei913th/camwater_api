<?php

use App\Enums\StatutFacture;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abonneId')
                ->constrained('abonnes')
                ->onDelete('cascade');
            $table->integer('montantTotal');
            $table->integer('consommation')->comment('En m³ ou kWh');
            $table->date('dateEmission');
            $table->enum('statut', StatutFacture::values())
                ->default(StatutFacture::EMISE);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['abonneId', 'statut']);
            $table->index('dateEmission');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
