<?php

use App\Helpers\DatabaseHelper;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;

if (!function_exists('runTransaction')) {
    /**
     * Exécuter une opération dans une transaction
     * 
     * @param callable $callback
     * @param string|null $context
     * @return mixed
     */
    function runTransaction(callable $callback, ?string $context = null)
    {
        return DatabaseHelper::runTransaction($callback, $context);
    }
}



if (!function_exists('api_success')) {
    /**
     * Crée une réponse API standardisée pour un succès.
     *
     * @param mixed $data Les données à retourner (optionnel, peut être un Resource, collection ou tableau)
     * @param string|null $message Message de succès (optionnel)
     * @param int $code Code HTTP (par défaut 200)
     *
     * @return JsonResponse
     *
     */
    function api_success(mixed $data = null, ?string $message = null, int $code = 200): JsonResponse
    {
        return ResponseHelper::success($data, $message, $code);
    }
}

if (!function_exists('api_error')) {
    /**
     * Crée une réponse API standardisée pour une erreur.
     *
     * @param string $message Message d'erreur
     * @param mixed $errors Détails additionnels ou erreurs de validation (optionnel)
     * @param int $code Code HTTP (par défaut 400)
     *
     * @return JsonResponse
     *
     * @example
     * return api_error('Entrée invalide', ['email' => 'Email requis'], 422);
     */
    function api_error(string $message, mixed $errors = null, int $code = 400): JsonResponse
    {
        return ResponseHelper::error($message, $errors, $code);
    }
}

if (!function_exists('api_created')) {
    /**
     * Crée une réponse standardisée après la création d'une ressource.
     *
     * @param mixed $data La ressource créée (optionnel)
     * @param string $message Message de succès (par défaut "Ressource créée avec succès")
     *
     * @return JsonResponse
     *
     */
    function api_created(mixed $data = null, string $message = 'Ressource créée avec succès'): JsonResponse
    {
        return ResponseHelper::created($data, $message);
    }
}

if (!function_exists('api_updated')) {
    /**
     * Crée une réponse standardisée après la mise à jour d'une ressource.
     *
     * @param mixed $data La ressource mise à jour (optionnel)
     * @param string $message Message de succès (par défaut "Ressource mise à jour avec succès")
     *
     * @return JsonResponse
     */
    function api_updated(mixed $data = null, string $message = 'Ressource mise à jour avec succès'): JsonResponse
    {
        return ResponseHelper::updated($data, $message);
    }
}

if (!function_exists('api_deleted')) {
    /**
     * Crée une réponse standardisée après la suppression d'une ressource.
     *
     * @param string $message Message de succès (par défaut "Ressource supprimée avec succès")
     *
     * @return JsonResponse
     *
     * @example
     * return api_deleted();
     */
    function api_deleted(string $message = 'Ressource supprimée avec succès'): JsonResponse
    {
        return ResponseHelper::deleted($message);
    }
}

if (!function_exists('api_not_found')) {
    /**
     * Crée une réponse standardisée 404 Not Found.
     *
     * @param string $message Message d'erreur (par défaut "Ressource non trouvée")
     *
     * @return JsonResponse
     *
     */
    function api_not_found(string $message = 'Ressource non trouvée'): JsonResponse
    {
        return ResponseHelper::notFound($message);
    }
}

if (!function_exists('api_unauthorized')) {
    /**
     * Crée une réponse standardisée 401 Unauthorized.
     *
     * @param string $message Message d'erreur (par défaut "Non autorisé")
     *
     * @return JsonResponse
     *
     * @example
     * return api_unauthorized('Authentification requise');
     */
    function api_unauthorized(string $message = 'Non autorisé'): JsonResponse
    {
        return ResponseHelper::unauthorized($message);
    }
}

if (!function_exists('api_forbidden')) {
    /**
     * Crée une réponse standardisée 403 Forbidden.
     *
     * @param string $message Message d'erreur (par défaut "Accès interdit")
     *
     * @return JsonResponse
     *
     * @example
     * return api_forbidden('Permissions insuffisantes');
     */
    function api_forbidden(string $message = 'Accès interdit'): JsonResponse
    {
        return ResponseHelper::forbidden($message);
    }
}

if (!function_exists('api_validation_error')) {
    /**
     * Crée une réponse standardisée pour les erreurs de validation.
     *
     * @param mixed $errors Erreurs de validation (tableau ou objet)
     * @param string $message Message d'erreur (par défaut "Erreur de validation")
     *
     * @return JsonResponse
     *
     * @example
     * return api_validation_error($validator->errors());
     */
    function api_validation_error(mixed $errors, string $message = 'Erreur de validation'): JsonResponse
    {
        return ResponseHelper::validationError($errors, $message);
    }
}

if (!function_exists('api_paginated')) {
    /**
     * Crée une réponse standardisée pour les listes paginées.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginatedData Données paginées
     * @param string|null $message Message de succès (optionnel)
     * @param string|null $resourceClass Classe de ressource API pour transformer les items (optionnel)
     *
     * @return JsonResponse
     *
     * @example
     * $users = User::paginate(10);
     * return api_paginated($users, 'Liste des utilisateurs', UserResource::class);
     */
    function api_paginated($paginatedData, ?string $message = null, ?string $resourceClass = null): JsonResponse
    {
        return ResponseHelper::paginated($paginatedData, $message, $resourceClass);
    }
}
