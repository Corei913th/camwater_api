<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Abonne\StoreAbonneRequest;
use App\Http\Requests\Abonne\UpdateAbonneRequest;
use App\Services\AbonneService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AbonneController extends Controller
{
    public function __construct(private readonly AbonneService $abonneService) {}

    #[OA\Get(path: '/api/abonnes', tags: ['Abonnes'], summary: 'Lister tous les abonnés', security: [new OA\SecurityScheme(name: 'bearerAuth')])]
    #[OA\Parameter(name: 'per_page', in: 'query', description: "Nombre d'éléments par page", schema: new OA\Schema(type: 'integer', default: 15))]
    #[OA\Parameter(name: 'ville', in: 'query', description: 'Filtrer par ville', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'typeAbonnement', in: 'query', description: "Filtrer par type d'abonnement", schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'search', in: 'query', description: 'Recherche par nom ou prénom', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Succès')]
    #[OA\Response(response: 500, description: 'Erreur serveur')]
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->query('per_page', 15);
            $filters = $request->only(['ville', 'typeAbonnement', 'search']);

            $abonnes = $this->abonneService->findAll($filters, $perPage);

            return api_paginated($abonnes, 'Liste des abonnés récupérée avec succès');
        } catch (QueryException $e) {
            return api_error('Erreur lors de la récupération des abonnés.', null, 500);
        }
    }

    #[OA\Post(path: '/api/abonnes', tags: ['Abonnes'], summary: 'Créer un nouvel abonné', security: [new OA\SecurityScheme(name: 'bearerAuth')])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['nom', 'prenom', 'ville', 'quartier', 'numeroCompteur', 'typeAbonnement'],
            properties: [
                new OA\Property(property: 'nom', type: 'string', example: 'Doe'),
                new OA\Property(property: 'prenom', type: 'string', example: 'John'),
                new OA\Property(property: 'ville', type: 'string', enum: ['Yaoundé', 'Douala', 'Bafoussam', 'Garoua']),
                new OA\Property(property: 'quartier', type: 'string', example: 'Etoudi'),
                new OA\Property(property: 'numeroCompteur', type: 'string', example: 'CPT1234'),
                new OA\Property(property: 'typeAbonnement', type: 'string', enum: ['DOMESTIQUE', 'PROFESSIONNEL']),
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Succès')]
    #[OA\Response(response: 422, description: 'Erreur de validation')]
    #[OA\Response(response: 500, description: 'Erreur serveur')]
    public function store(StoreAbonneRequest $request): JsonResponse
    {
        try {
            $abonne = $this->abonneService->create($request->validated());

            return api_created($abonne, 'Abonné créé avec succès.');
        } catch (QueryException $e) {
            return api_error('Erreur lors de la création de l\'abonné.', null, 500);
        }
    }

    #[OA\Get(path: '/api/abonnes/{id}', tags: ['Abonnes'], summary: 'Afficher un abonné', security: [new OA\SecurityScheme(name: 'bearerAuth')])]
    #[OA\Parameter(name: 'id', in: 'path', description: "ID de l'abonné", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Succès')]
    #[OA\Response(response: 404, description: 'Abonné introuvable')]
    public function show(int $id): JsonResponse
    {
        try {
            $abonne = $this->abonneService->finById($id);

            return api_success($abonne, 'Abonné récupéré avec succès.');
        } catch (ModelNotFoundException $e) {
            return api_not_found('Abonné introuvable.');
        }
    }

    #[OA\Put(path: '/api/abonnes/{id}', tags: ['Abonnes'], summary: 'Modifier un abonné', security: [new OA\SecurityScheme(name: 'bearerAuth')])]
    #[OA\Parameter(name: 'id', in: 'path', description: "ID de l'abonné", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'nom', type: 'string', example: 'Doe'),
                new OA\Property(property: 'prenom', type: 'string', example: 'John'),
                new OA\Property(property: 'ville', type: 'string', enum: ['Yaoundé', 'Douala', 'Bafoussam', 'Garoua']),
                new OA\Property(property: 'quartier', type: 'string', example: 'Etoudi'),
                new OA\Property(property: 'typeAbonnement', type: 'string', enum: ['DOMESTIQUE', 'PROFESSIONNEL']),
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Succès')]
    #[OA\Response(response: 404, description: 'Abonné introuvable')]
    #[OA\Response(response: 422, description: 'Erreur de validation')]
    #[OA\Response(response: 500, description: 'Erreur serveur')]
    public function update(UpdateAbonneRequest $request, int $id): JsonResponse
    {
        try {
            $abonne = $this->abonneService->update($id, $request->validated());

            return api_success($abonne, 'Abonné mis à jour avec succès.');
        } catch (ModelNotFoundException $e) {
            return api_not_found('Abonné introuvable.');
        } catch (QueryException $e) {
            return api_error('Erreur lors de la mise à jour de l\'abonné.', null, 500);
        }
    }

    #[OA\Delete(path: '/api/abonnes/{id}', tags: ['Abonnes'], summary: 'Supprimer un abonné', security: [new OA\SecurityScheme(name: 'bearerAuth')])]
    #[OA\Parameter(name: 'id', in: 'path', description: "ID de l'abonné", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Succès')]
    #[OA\Response(response: 404, description: 'Abonné introuvable')]
    #[OA\Response(response: 500, description: 'Erreur serveur')]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->abonneService->delete($id);

            return api_deleted('Abonné supprimé avec succès.');
        } catch (ModelNotFoundException $e) {
            return api_not_found('Abonné introuvable.');
        } catch (QueryException $e) {
            return api_error('Erreur lors de la suppression de l\'abonné.', null, 500);
        }
    }
}
