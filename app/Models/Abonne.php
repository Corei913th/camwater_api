<?php

namespace App\Models;

use App\Enums\TypeAbonnement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abonne extends Model
{
    use HasFactory, SoftDeletes;


    public static $snakeAttributes = false;

    protected $table = 'abonnes';

    protected $fillable = [
        'nom',
        'prenom',
        'quartier',
        'ville',
        'numeroCompteur',
        'typeAbonnement',
    ];

    protected $casts = [
        'typeAbonnement' => TypeAbonnement::class,
    ];



    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class, 'abonneId');
    }



    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }
}
