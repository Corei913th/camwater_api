<?php

namespace App\Services;

use App\Enums\StatutFacture;
use App\Models\Facture;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class FactureService
{
    public function __construct(
        private readonly Facture $model,
        private readonly CalculateurFacture $calculateur,
        private readonly AbonneService $abonneService
    ) {}

    /**
     * Génère une facture pour un abonné.
     * Récupère l'abonné, déduit son typeAbonnement, puis délègue le calcul à CalculateurFacture.
     *
     * @param  array  $data  Doit contenir : abonneId, consommation, dateEmission
     *
     * @throws ModelNotFoundException|InvalidArgumentException|QueryException
     */
    public function generate(array $data): Facture
    {
        try {
            $abonne = $this->abonneService->finById((int) $data['abonneId']);

            $montantTotal = $this->calculateur->calculerMontant(
                (int) $data['consommation'],
                $abonne->typeAbonnement
            );

            return $this->model->create([
                'abonneId' => $abonne->id,
                'consommation' => $data['consommation'],
                'dateEmission' => now()->toDateString(),
                'statut' => StatutFacture::EMISE->value,
                'montantTotal' => $montantTotal,
            ]);
        } catch (ModelNotFoundException $e) {
            Log::warning('FactureService@generate - abonne not found', ['abonneId' => $data['abonneId']]);
            throw $e;
        } catch (InvalidArgumentException $e) {
            Log::error('FactureService@generate - invalid data', ['error' => $e->getMessage()]);
            throw $e;
        } catch (QueryException $e) {
            Log::error('FactureService@generate failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Retourne une liste paginée de factures.
     */
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with('abonne')->newQuery();

        if (! empty($filters['abonneId'])) {
            $query->where('abonneId', $filters['abonneId']);
        }

        if (! empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }

        if (! empty($filters['dateEmission'])) {
            $query->whereDate('dateEmission', $filters['dateEmission']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Retourne une facture par son identifiant.
     *
     * @throws ModelNotFoundException
     */
    public function findById(int $id): Facture
    {
        try {
            return $this->model->with('abonne')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::warning('FactureService@findById - not found', ['id' => $id]);
            throw $e;
        }
    }
}
