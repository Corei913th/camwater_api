<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facture\GenererFactureRequest;
use App\Services\FactureService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use OpenApi\Attributes as OA;

class FactureController extends Controller
{
    public function __construct(private readonly FactureService $factureService) {}

    #[OA\Get(path: '/api/factures', tags: ['Factures'], summary: 'Lister toutes les factures')]
    #[OA\Parameter(name: 'per_page', in: 'query', description: "Nombre d'éléments par page", schema: new OA\Schema(type: 'integer', default: 15))]
    #[OA\Parameter(name: 'abonneId', in: 'query', description: "Filtrer par ID d'abonné", schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'statut', in: 'query', description: 'Filtrer par statut (Payé, Impayé, etc.)', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'dateEmission', in: 'query', description: "Filtrer par date d'émission", schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Response(response: 200, description: 'Succès')]
    #[OA\Response(response: 500, description: 'Erreur serveur')]
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->query('per_page', 15);
            $filters = $request->only(['abonneId', 'statut', 'dateEmission']);

            $factures = $this->factureService->findAll($filters, $perPage);

            return api_paginated($factures, 'Liste des factures récupérée avec succès');
        } catch (QueryException $e) {
            return api_error('Erreur lors de la récupération des factures.', null, 500);
        }
    }

    #[OA\Post(path: '/api/factures/generer', tags: ['Factures'], summary: 'Générer une facture pour un abonné')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['abonneId', 'consommation'],
            properties: [
                new OA\Property(property: 'abonneId', type: 'integer', example: 1),
                new OA\Property(property: 'consommation', type: 'integer', example: 100),
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Succès')]
    #[OA\Response(response: 404, description: 'Pas trouvé')]
    #[OA\Response(response: 422, description: 'Erreur de validation')]
    #[OA\Response(response: 500, description: 'Erreur serveur')]
    public function generer(GenererFactureRequest $request): JsonResponse
    {
        try {
            $facture = $this->factureService->generate($request->validated());

            return api_created($facture, 'Facture générée avec succès.');
        } catch (ModelNotFoundException $e) {
            return api_not_found('Abonné introuvable.');
        } catch (InvalidArgumentException $e) {
            return api_error($e->getMessage(), null, 422);
        } catch (QueryException $e) {
            return api_error('Erreur lors de la génération de la facture.', null, 500);
        }
    }

    #[OA\Get(path: '/api/factures/{id}', tags: ['Factures'], summary: 'Consulter une facture')]
    #[OA\Parameter(name: 'id', in: 'path', description: 'ID de la facture', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Succès')]
    #[OA\Response(response: 404, description: 'Facture introuvable')]
    public function show(int $id): JsonResponse
    {
        try {
            $facture = $this->factureService->findById($id);

            return api_success($facture, 'Facture récupérée avec succès.');
        } catch (ModelNotFoundException $e) {
            return api_not_found('Facture introuvable.');
        }
    }
}
