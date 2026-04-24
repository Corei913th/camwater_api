@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Bienvenue sur CamwaterPRO')
@section('header_subtitle', 'Aperçu global de votre exploitation')

@section('content')
<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-icon" style="background: #dbeafe; color: #2563eb;">
            <i data-lucide="users"></i>
        </div>
        <div class="stat-info">
            <h3>Total Abonnés</h3>
            <p>{{ $totalAbonnes }}</p>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background: #fef9c3; color: #854d0e;">
            <i data-lucide="file-text"></i>
        </div>
        <div class="stat-info">
            <h3>Factures Émises</h3>
            <p>{{ $totalFactures }}</p>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background: #dcfce7; color: #166534;">
            <i data-lucide="banknote"></i>
        </div>
        <div class="stat-info">
            <h3>Chiffre d'Affaires</h3>
            <p>{{ number_format($caTotal, 0, ',', ' ') }} XAF</p>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background: #fee2e2; color: #991b1b;">
            <i data-lucide="alert-triangle"></i>
        </div>
        <div class="stat-info">
            <h3>Réclamations</h3>
            <p>12</p>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.125rem; font-weight: 600;">Derniers Abonnés</h2>
            <a href="{{ route('web.abonnes.index') }}" style="color: var(--primary); font-size: 0.875rem; text-decoration: none;">Voir tout</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nom & Prénom</th>
                    <th>Quartier</th>
                    <th>Compteur</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentAbonnes as $abonne)
                <tr>
                    <td style="font-weight: 500;">{{ $abonne->nom }} {{ $abonne->prenom }}</td>
                    <td>{{ $abonne->quartier }}</td>
                    <td><code>{{ $abonne->numeroCompteur }}</code></td>
                    <td style="color: var(--text-muted); font-size: 0.875rem;">{{ $abonne->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">Actions Rapides</h2>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="#" class="btn btn-primary" style="justify-content: center;">
                <i data-lucide="plus"></i> Nouvel Abonné
            </a>
            <a href="{{ route('web.factures.create') }}" class="btn" style="width: 100%; justify-content: center; background: #f1f5f9; color: var(--text-main);">
                <i data-lucide="refresh-cw"></i> Générer les Factures
            </a>
        </div>
    </div>
</div>
@endsection
