@extends('layouts.app')

@section('title', 'Factures')
@section('header_title', 'Gestion des Factures')
@section('header_subtitle', 'Historique des factures et suivi des paiements')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 1rem;">
            <select style="padding: 0.5rem; border-radius: 0.5rem; border: 1px solid var(--border); outline: none;">
                <option>Tous les statuts</option>
                <option>Payée</option>
                <option>En attente</option>
                <option>Annulée</option>
            </select>
            <select style="padding: 0.5rem; border-radius: 0.5rem; border: 1px solid var(--border); outline: none;">
                <option>Derniers 30 jours</option>
                <option>Mois dernier</option>
                <option>Année 2026</option>
            </select>
        </div>
        <a href="{{ route('web.factures.create') }}" class="btn btn-primary">
            <i data-lucide="refresh-cw"></i> Générer les Factures
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Abonné</th>
                <th>Consommation</th>
                <th>Montant (XAF)</th>
                <th>Date d'émission</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factures as $facture)
            <tr>
                <td style="font-weight: 500;"><code>#FAC-{{ str_pad($facture->id, 5, '0', STR_PAD_LEFT) }}</code></td>
                <td>
                    <div style="font-weight: 500;">{{ $facture->abonne->nom }} {{ $facture->abonne->prenom }}</div>
                    <div style="color: var(--text-muted); font-size: 0.75rem;">Compteur: {{ $facture->abonne->numeroCompteur }}</div>
                </td>
                <td>{{ $facture->consommation }} m³</td>
                <td style="font-weight: 600;">{{ number_format($facture->montantTotal, 0, ',', ' ') }}</td>
                <td style="color: var(--text-muted); font-size: 0.875rem;">{{ $facture->dateEmission }}</td>
                <td>
                    <span class="badge {{ $facture->statut == 'PAYEE' ? 'badge-success' : ($facture->statut == 'EN_ATTENTE' ? 'badge-warning' : 'badge-danger') }}">
                        {{ $facture->statut }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('web.factures.show', $facture->id) }}" style="color: var(--primary);"><i data-lucide="eye" style="width: 18px;"></i></a>
                        <button style="border: none; background: none; color: var(--text-muted); cursor: pointer;"><i data-lucide="download" style="width: 18px;"></i></button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <p style="color: var(--text-muted); font-size: 0.875rem;">Affichage de {{ $factures->firstItem() }} à {{ $factures->lastItem() }} sur {{ $factures->total() }} factures</p>
        <div style="display: flex; gap: 0.25rem;">
            @if($factures->onFirstPage())
                <span class="btn" style="padding: 0.25rem 0.5rem; background: #f1f5f9; color: #94a3b8; border: 1px solid var(--border); cursor: not-allowed;">Précédent</span>
            @else
                <a href="{{ $factures->previousPageUrl() }}" class="btn" style="padding: 0.25rem 0.5rem; background: white; border: 1px solid var(--border);">Précédent</a>
            @endif

            <span class="btn" style="padding: 0.25rem 0.5rem; background: var(--primary); color: white; border: none;">{{ $factures->currentPage() }}</span>

            @if($factures->hasMorePages())
                <a href="{{ $factures->nextPageUrl() }}" class="btn" style="padding: 0.25rem 0.5rem; background: white; border: 1px solid var(--border);">Suivant</a>
            @else
                <span class="btn" style="padding: 0.25rem 0.5rem; background: #f1f5f9; color: #94a3b8; border: 1px solid var(--border); cursor: not-allowed;">Suivant</span>
            @endif
        </div>
    </div>
</div>
@endsection
