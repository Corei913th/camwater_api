<?php

namespace App\Http\Requests\Abonne;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbonneRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Règles de validation pour la création d'un abonné.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'nom'            => ['required', 'string'],
      'prenom'         => ['required', 'string'],
      'ville'          => ['required', 'string', 'in:Yaoundé,Douala,Bafoussam,Garoua'],
      'quartier'       => ['required', 'string'],
      'numeroCompteur' => ['required', 'string', 'unique:abonnes,numeroCompteur'],
      'typeAbonnement' => ['required', 'string', 'in:DOMESTIQUE,PROFESSIONNEL'],
    ];
  }

  public function messages(): array
  {
    return [
      'nom.required'            => 'Le nom est obligatoire.',
      'nom.string'              => 'Le nom doit être une chaîne de caractères.',
      'prenom.required'         => 'Le prénom est obligatoire.',
      'prenom.string'           => 'Le prénom doit être une chaîne de caractères.',
      'ville.required'          => 'La ville est obligatoire.',
      'ville.in'                => 'La ville doit être l\'une des suivantes : Yaoundé, Douala, Bafoussam, Garoua.',
      'quartier.required'       => 'Le quartier est obligatoire.',
      'quartier.string'         => 'Le quartier doit être une chaîne de caractères.',
      'numeroCompteur.required' => 'Le numéro de compteur est obligatoire.',
      'numeroCompteur.unique'   => 'Ce numéro de compteur est déjà utilisé.',
      'typeAbonnement.required' => 'Le type d\'abonnement est obligatoire.',
      'typeAbonnement.in'       => 'Le type d\'abonnement doit être : DOMESTIQUE ou PROFESSIONNEL.',
    ];
  }
}
