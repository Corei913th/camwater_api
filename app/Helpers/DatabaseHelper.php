<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Helper pour les opérations de base de données
 */
class DatabaseHelper
{
  /**
   * Exécuter une opération dans une transaction
   * 
   * @param callable $callback Fonction à exécuter
   * @param string|null $context Contexte pour le logging en cas d'erreur
   * @return mixed
   * @throws \Exception
   */
  public static function runTransaction(callable $callback, ?string $context = null)
  {
    try {
      return DB::transaction($callback);
    } catch (\Exception $e) {
      if ($context) {
        Log::error("Transaction failed: {$context}", [
          'error' => $e->getMessage(),
          'trace' => $e->getTraceAsString()
        ]);
      }
      throw $e;
    }
  }

  /**
   * Logger une erreur de service avec contexte
   * 
   * @param string $message Message d'erreur
   * @param \Exception $exception Exception levée
   * @param array $context Contexte additionnel
   * @return void
   */
  public static function logServiceError(string $message, \Exception $exception, array $context = []): void
  {
    Log::error($message, array_merge([
      'error' => $exception->getMessage(),
      'file' => $exception->getFile(),
      'line' => $exception->getLine(),
    ], $context));
  }

  /**
   * Vérifier si un code existe
   * 
   * @param string $modelClass Classe du modèle
   * @param string $column Nom de la colonne
   * @param mixed $value Valeur à vérifier
   * @param string|null $excludeId ID à exclure (pour update)
   * @return bool
   */
  public static function codeExists(string $modelClass, string $column, $value, ?string $excludeId = null): bool
  {
    $query = $modelClass::where($column, $value);

    if ($excludeId) {
      $query->where('id', '!=', $excludeId);
    }

    return $query->exists();
  }

  /**
   * Vérifier si une entité a des dépendances
   * 
   * @param mixed $model Instance du modèle
   * @param string $relation Nom de la relation
   * @return bool
   */
  public static function hasDependencies($model, string $relation): bool
  {
    return $model->{$relation}()->exists();
  }

  /**
   * Trouver par ID ou lever une exception personnalisée
   * 
   * @param string $modelClass Classe du modèle
   * @param string $id ID à rechercher
   * @param string $exceptionClass Classe d'exception à lever
   * @return mixed
   * @throws \Exception
   */
  public static function findOrFail(string $modelClass, string $id, string $exceptionClass)
  {
    try {
      return $modelClass::findOrFail($id);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      throw $exceptionClass::notFound($id);
    }
  }
}
