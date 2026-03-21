<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facture extends Model
{
    use HasFactory, SoftDeletes;

    public static $snakeAttributes = false;

    protected $fillable = [
        'abonneId',
        'consommation',
        'typeAbonnement',
        'dateEmission',
        'statut',
        'montantTotal',
    ];

    protected $casts = [
        'dateEmission' => 'date',
    ];

    public function abonne(): BelongsTo
    {
        return $this->belongsTo(Abonne::class, 'abonneId');
    }
}
