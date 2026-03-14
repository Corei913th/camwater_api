<?php

namespace App\Services;

use App\Enums\TypeAbonnement;
use InvalidArgumentException;

class CalculateurFacture
{
  /**
   * Calcule le montant total d'une facture selon la consommation
   * et le type d'abonnement, en appliquant la grille tarifaire.
   *
   * @param  int             $consommation   Consommation en m³ (entier strictement positif)
   * @param  TypeAbonnement  $typeAbonnement
   * @return int             Montant total en FCFA
   * @throws InvalidArgumentException Si la consommation est invalide ou le type inconnu
   */
  public function calculerMontant(int $consommation, TypeAbonnement $typeAbonnement): int
  {
    if ($consommation <= 0) {
      throw new InvalidArgumentException(
        "La consommation doit être un entier strictement positif, [{$consommation}] fourni."
      );
    }

    return match ($typeAbonnement) {
      TypeAbonnement::DOMESTIQUE    => self::tarifDomestique($consommation),
      TypeAbonnement::PROFESSIONNEL => self::tarifProfessionnel($consommation),
      default                       => throw new InvalidArgumentException(
        "Type d'abonnement inconnu : [{$typeAbonnement->value}]."
      ),
    };
  }

  /**
   * Applique le calcul par tranches progressives pour le tarif Domestique.
   *
   * @param  int  $consommation
   * @return int
   */
  private static function tarifDomestique(int $consommation): int
  {
    if ($consommation <= 10) {
      return $consommation * 350;
    }

    if ($consommation <= 20) {
      return (10 * 350) + (($consommation - 10) * 550);
    }

    return (10 * 350) + (10 * 550) + (($consommation - 20) * 780);
  }

  /**
   * Applique le tarif forfaitaire Professionnel :
   * forfait fixe + 950 FCFA/m³, arrondi à l'entier supérieur.
   *
   * @param  int  $consommation
   * @return int
   */
  private static function tarifProfessionnel(int $consommation): int
  {
    $forfait = config('billing.forfait_professionnel', 8500);

    return (int) ceil($forfait + ($consommation * 950));
  }
}
