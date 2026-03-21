<?php

namespace App\Models;

use App\Enums\StatutReclamation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reclamation extends Model
{
    use HasFactory, SoftDeletes;

    public static $snakeAttributes = false;

    protected $table = 'reclamations';

    protected $fillable = [
        'factureId',
        'contenu',
        'statut',
        'reponse',
        'dateReponse',
    ];

    protected $casts = [
        'statut' => StatutReclamation::class,
        'dateReponse' => 'datetime',
    ];

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class, 'factureId');
    }
}
