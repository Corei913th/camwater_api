@extends('layouts.app')

@section('title', 'Abonnés')
@section('header_title', 'Gestion des Abonnés')
@section('header_subtitle', 'Consultez et gérez la liste des abonnés au réseau')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <form action="{{ route('web.abonnes.index') }}" method="GET" style="position: relative; width: 300px;">
            <i data-lucide="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 16px; color: var(--text-muted);"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un abonné..." style="width: 100%; padding: 0.5rem 1rem 0.5rem 2.5rem; border-radius: 0.5rem; border: 1px solid var(--border); outline: none;">
        </form>
        <a href="{{ route('web.abonnes.create') }}" class="btn btn-primary">
            <i data-lucide="plus"></i> Ajouter un Abonné
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Identifiant</th>
                <th>Nom & Prénom</th>
                <th>Localisation</th>
                <th>Compteur</th>
                <th>Type</th>
                <th>Date d'adhésion</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($abonnes as $abonne)
            <tr>
                <td><span style="color: var(--text-muted); font-size: 0.875rem;">#{{ str_pad($abonne->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                <td style="font-weight: 500;">{{ $abonne->nom }} {{ $abonne->prenom }}</td>
                <td>
                    <div style="font-size: 0.875rem;">{{ $abonne->ville }}</div>
                    <div style="color: var(--text-muted); font-size: 0.75rem;">{{ $abonne->quartier }}</div>
                </td>
                <td><code>{{ $abonne->numeroCompteur }}</code></td>
                <td>
                    <span class="badge {{ $abonne->typeAbonnement == 'DOMESTIQUE' ? 'badge-success' : 'badge-warning' }}">
                        {{ $abonne->typeAbonnement }}
                    </span>
                </td>
                <td style="color: var(--text-muted); font-size: 0.875rem;">{{ $abonne->created_at->format('d/m/Y') }}</td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <button style="border: none; background: none; color: var(--primary); cursor: pointer;"><i data-lucide="edit-2" style="width: 18px;"></i></button>
                        <button style="border: none; background: none; color: var(--danger); cursor: pointer;"><i data-lucide="trash-2" style="width: 18px;"></i></button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <p style="color: var(--text-muted); font-size: 0.875rem;">Affichage de {{ $abonnes->firstItem() }} à {{ $abonnes->lastItem() }} sur {{ $abonnes->total() }} abonnés</p>
        <div style="display: flex; gap: 0.25rem;">
            @if($abonnes->onFirstPage())
                <span class="btn" style="padding: 0.25rem 0.5rem; background: #f1f5f9; color: #94a3b8; border: 1px solid var(--border); cursor: not-allowed;">Précédent</span>
            @else
                <a href="{{ $abonnes->previousPageUrl() }}" class="btn" style="padding: 0.25rem 0.5rem; background: white; border: 1px solid var(--border);">Précédent</a>
            @endif

            <span class="btn" style="padding: 0.25rem 0.5rem; background: var(--primary); color: white; border: none;">{{ $abonnes->currentPage() }}</span>

            @if($abonnes->hasMorePages())
                <a href="{{ $abonnes->nextPageUrl() }}" class="btn" style="padding: 0.25rem 0.5rem; background: white; border: 1px solid var(--border);">Suivant</a>
            @else
                <span class="btn" style="padding: 0.25rem 0.5rem; background: #f1f5f9; color: #94a3b8; border: 1px solid var(--border); cursor: not-allowed;">Suivant</span>
            @endif
        </div>
    </div>
</div>
@endsection
