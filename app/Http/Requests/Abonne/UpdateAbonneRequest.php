<?php

namespace App\Http\Requests\Abonne;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAbonneRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Règles de validation pour la mise à jour d'un abonné.
   *
   * @return array
   */
  public function rules(): array
  {
    $id = $this->route('id');

    return [
      'nom'            => ['sometimes', 'string'],
      'prenom'         => ['sometimes', 'string'],
      'ville'          => ['sometimes', 'string', 'in:Yaoundé,Douala,Bafoussam,Garoua'],
      'quartier'       => ['sometimes', 'string'],
      'numeroCompteur' => ['sometimes', 'string', "unique:abonnes,numeroCompteur,{$id}"],
      'typeAbonnement' => ['sometimes', 'string', 'in:DOMESTIQUE,PROFESSIONNEL'],
    ];
  }

  public function messages(): array
  {
    return [
      'nom.string'              => 'Le nom doit être une chaîne de caractères.',
      'prenom.string'           => 'Le prénom doit être une chaîne de caractères.',
      'ville.in'                => 'La ville doit être l\'une des suivantes : Yaoundé, Douala, Bafoussam, Garoua.',
      'quartier.string'         => 'Le quartier doit être une chaîne de caractères.',
      'numeroCompteur.unique'   => 'Ce numéro de compteur est déjà utilisé.',
      'typeAbonnement.in'       => 'Le type d\'abonnement doit être : DOMESTIQUE ou PROFESSIONNEL.',
    ];
  }
}
