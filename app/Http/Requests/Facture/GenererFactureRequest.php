<?php

namespace App\Http\Requests\Facture;

use Illuminate\Foundation\Http\FormRequest;

class GenererFactureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la génération d'une facture.
     */
    public function rules(): array
    {
        return [
            'abonneId' => ['required', 'integer', 'exists:abonnes,id'],
            'consommation' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'abonneId.required' => 'L\'identifiant de l\'abonné est obligatoire.',
            'abonneId.integer' => 'L\'identifiant de l\'abonné doit être un entier.',
            'abonneId.exists' => 'L\'abonné spécifié n\'existe pas.',
            'consommation.required' => 'La consommation est obligatoire.',
            'consommation.integer' => 'La consommation doit être un entier.',
            'consommation.min' => 'La consommation doit être strictement supérieure à 0.',
        ];
    }
}
