<?php

namespace App\Services;

use App\Models\Abonne;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AbonneService
{
    public function __construct(private readonly Abonne $model) {}

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (! empty($filters['ville'])) {
            $query->where('ville', $filters['ville']);
        }

        if (! empty($filters['typeAbonnement'])) {
            $query->where('typeAbonnement', $filters['typeAbonnement']);
        }

        if (! empty($filters['search'])) {
            $search = mb_strtolower($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nom) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(prenom) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(numeroCompteur) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Abonne
    {
        return $this->model->create($data);
    }

    public function finById(int $id): Abonne
    {
        return $this->model->findOrFail($id);
    }

    public function update(int $id, array $data): Abonne
    {
        $abonne = $this->finById($id);
        $abonne->update($data);

        return $abonne->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->finById($id)->delete();
    }

    /**
     * Statistiques par ville.
     */
    public function getStats(): Collection
    {
        return $this->model
            ->selectRaw('ville, COUNT(*) as total, typeAbonnement')
            ->groupBy('ville', 'typeAbonnement')
            ->get();
    }
}
