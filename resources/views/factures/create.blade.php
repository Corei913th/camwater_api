@extends('layouts.app')

@section('title', 'Générer une Facture')
@section('header_title', 'Nouvelle Facture')
@section('header_subtitle', 'Saisissez la consommation pour générer une facture')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form action="{{ route('web.factures.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Abonné</label>
            <select name="abonneId" required style="width: 100%; padding: 0.75rem; border-radius: 0.75rem; border: 1px solid {{ $errors->has('abonneId') ? 'var(--danger)' : 'var(--border)' }}; outline: none;">
                <option value="">Sélectionnez un abonné</option>
                @foreach($abonnes as $abonne)
                    <option value="{{ $abonne->id }}" {{ old('abonneId') == $abonne->id ? 'selected' : '' }}>
                        {{ $abonne->nom }} {{ $abonne->prenom }} ({{ $abonne->numeroCompteur }})
                    </option>
                @endforeach
            </select>
            @error('abonneId')
                <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label>Consommation (en m³)</label>
            <input type="number" name="consommation" value="{{ old('consommation') }}" required placeholder="Ex: 45" style="{{ $errors->has('consommation') ? 'border-color: var(--danger);' : '' }}">
            @error('consommation')
                <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('web.factures.index') }}" class="btn" style="background: #f1f5f9; color: var(--text-main);">Annuler</a>
            <button type="submit" class="btn btn-primary">Générer la Facture</button>
        </div>
    </form>
</div>
@endsection
