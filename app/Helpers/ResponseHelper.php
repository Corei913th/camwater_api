<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;


class ResponseHelper
{
    /**
     * Create a standardized success response
     *
     * @param mixed $data The data to include in the response (optional) - often an API Resource or array
     * @param string|null $message Success message (optional)
     * @param int $code HTTP status code (default: 200)
     *
     * @return JsonResponse JSON response with success structure
     *
     * @example
     * // Controller usage with API Resources
     * $user = User::find(1);
     * return ResponseHelper::success(new \App\Http\Resources\UserResource($user), 'User retrieved successfully');
     * // Returns: {"success": true, "message": "User retrieved successfully", "data": {...}}
     *
     * @example
     * // Controller usage with collections
     * $concours = Concours::all();
     * return ResponseHelper::success(\App\Http\Resources\ConcoursResource::collection($concours), 'Concours list retrieved');
     * // Returns: {"success": true, "message": "Concours list retrieved", "data": [...]}
     */
    public static function success($data = null, string $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Create a standardized error response
     *
     * @param string $message Error message
     * @param mixed $errors Additional error details or validation errors (optional)
     * @param int $code HTTP status code (default: 400)
     *
     * @return JsonResponse JSON response with error structure
     *
     * @example
     * ResponseHelper::error('Invalid input', ['email' => 'Email is required'], 422);
     * // Returns: {"success": false, "message": "Invalid input", "errors": {"email": "Email is required"}}
     */
    public static function error(string $message, $errors = null, int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Create a standardized resource creation response
     *
     * @param mixed $data The created resource data (optional) - typically an API Resource
     * @param string $message Success message (default: 'Ressource créée avec succès')
     *
     * @return JsonResponse JSON response with 201 status code
     *
     * @example
     * // Controller usage after creating a resource
     * $concours = Concours::create($validatedData);
     * return ResponseHelper::created(new \App\Http\Resources\ConcoursResource($concours), 'Concours created successfully');
     * // Returns: {"success": true, "message": "Concours created successfully", "data": {...}} with HTTP 201
     */
    public static function created($data = null, string $message = 'Ressource créée avec succès'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * Create a standardized resource update response
     *
     * @param mixed $data The updated resource data (optional) - typically an API Resource
     * @param string $message Success message (default: 'Ressource mise à jour avec succès')
     *
     * @return JsonResponse JSON response with success structure
     *
     * @example
     * // Controller usage after updating a resource
     * $departement->update($validatedData);
     * return ResponseHelper::updated($departement, 'Departement updated successfully');
     * // Returns: {"success": true, "message": "Departement updated successfully", "data": {...}}
     */
    public static function updated($data = null, string $message = 'Ressource mise à jour avec succès'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Create a standardized resource deletion response
     *
     * @param string $message Success message (default: 'Ressource supprimée avec succès')
     *
     * @return JsonResponse JSON response with success structure (no data)
     *
     * @example
     * ResponseHelper::deleted('User deleted successfully');
     * // Returns: {"success": true, "message": "User deleted successfully"}
     */
    public static function deleted(string $message = 'Ressource supprimée avec succès'): JsonResponse
    {
        return self::success(null, $message, 200);
    }

    /**
     * Create a standardized 404 Not Found response
     *
     * @param string $message Error message (default: 'Ressource non trouvée')
     *
     * @return JsonResponse JSON response with 404 status code
     *
     * @example
     * ResponseHelper::notFound('User not found');
     * // Returns: {"success": false, "message": "User not found"} with HTTP 404
     */
    public static function notFound(string $message = 'Ressource non trouvée'): JsonResponse
    {
        return self::error($message, null, 404);
    }

    /**
     * Create a standardized 401 Unauthorized response
     *
     * @param string $message Error message (default: 'Non autorisé')
     *
     * @return JsonResponse JSON response with 401 status code
     *
     * @example
     * ResponseHelper::unauthorized('Authentication required');
     * // Returns: {"success": false, "message": "Authentication required"} with HTTP 401
     */
    public static function unauthorized(string $message = 'Non autorisé'): JsonResponse
    {
        return self::error($message, null, 401);
    }

    /**
     * Create a standardized 403 Forbidden response
     *
     * @param string $message Error message (default: 'Accès interdit')
     *
     * @return JsonResponse JSON response with 403 status code
     *
     * @example
     * ResponseHelper::forbidden('Insufficient permissions');
     * // Returns: {"success": false, "message": "Insufficient permissions"} with HTTP 403
     */
    public static function forbidden(string $message = 'Accès interdit'): JsonResponse
    {
        return self::error($message, null, 403);
    }

    /**
     * Create a standardized validation error response
     *
     * @param mixed $errors Validation errors array or object
     * @param string $message Error message (default: 'Erreur de validation')
     *
     * @return JsonResponse JSON response with 422 status code and validation errors
     *
     * @example
     * ResponseHelper::validationError(['email' => 'Email is required'], 'Validation failed');
     * // Returns: {"success": false, "message": "Validation failed", "errors": {"email": "Email is required"}} with HTTP 422
     */
    public static function validationError($errors, string $message = 'Erreur de validation'): JsonResponse
    {
        return self::error($message, $errors, 422);
    }

    /**
     * Create a standardized paginated response
     *
     * Formats Laravel paginated data with optional resource transformation and includes
     * comprehensive pagination metadata in the response.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginatedData Laravel paginated data
     * @param string|null $message Optional success message
     * @param string|null $resourceClass Optional API resource class for data transformation
     *
     * @return JsonResponse JSON response with paginated data and metadata
     *
     * @example
     * $users = User::paginate(10);
     * ResponseHelper::paginated($users, 'Users retrieved', UserResource::class);
     * // Returns: {
     * //   "success": true,
     * //   "message": "Users retrieved",
     * //   "data": [...],
     * //   "meta": {
     * //     "current_page": 1,
     * //     "last_page": 5,
     * //     "per_page": 10,
     * //     "total": 50,
     * //     "from": 1,
     * //     "to": 10
     * //   }
     * // }
     */
    public static function paginated($paginatedData, string $message = null, ?string $resourceClass = null): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        $items = $paginatedData->items();

        // Si une classe de ressource est fournie, transformer les items
        if ($resourceClass && class_exists($resourceClass)) {
            $items = $resourceClass::collection($items);
        }

        $response['data'] = $items;
        $response['meta'] = [
            'current_page' => $paginatedData->currentPage(),
            'last_page' => $paginatedData->lastPage(),
            'per_page' => $paginatedData->perPage(),
            'total' => $paginatedData->total(),
            'from' => $paginatedData->firstItem(),
            'to' => $paginatedData->lastItem(),
        ];

        return response()->json($response, 200);
    }
}
