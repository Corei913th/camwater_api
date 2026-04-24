@extends('layouts.app')

@section('title', 'Ajouter un Abonné')
@section('header_title', 'Nouvel Abonné')
@section('header_subtitle', 'Enregistrez un nouveau client dans le système')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('web.abonnes.store') }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="Ex: DOE" style="{{ $errors->has('nom') ? 'border-color: var(--danger);' : '' }}">
                @error('nom')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="prenom" value="{{ old('prenom') }}" required placeholder="Ex: John" style="{{ $errors->has('prenom') ? 'border-color: var(--danger);' : '' }}">
                @error('prenom')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Ville</label>
                <select name="ville" required style="width: 100%; padding: 0.75rem; border-radius: 0.75rem; border: 1px solid {{ $errors->has('ville') ? 'var(--danger)' : 'var(--border)' }}; outline: none;">
                    <option value="">Sélectionnez une ville</option>
                    <option value="Yaoundé" {{ old('ville') == 'Yaoundé' ? 'selected' : '' }}>Yaoundé</option>
                    <option value="Douala" {{ old('ville') == 'Douala' ? 'selected' : '' }}>Douala</option>
                    <option value="Bafoussam" {{ old('ville') == 'Bafoussam' ? 'selected' : '' }}>Bafoussam</option>
                    <option value="Garoua" {{ old('ville') == 'Garoua' ? 'selected' : '' }}>Garoua</option>
                </select>
                @error('ville')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Quartier</label>
                <input type="text" name="quartier" value="{{ old('quartier') }}" required placeholder="Ex: Etoudi" style="{{ $errors->has('quartier') ? 'border-color: var(--danger);' : '' }}">
                @error('quartier')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Numéro de Compteur</label>
                <input type="text" name="numeroCompteur" value="{{ old('numeroCompteur') }}" required placeholder="Ex: CPT8822" style="{{ $errors->has('numeroCompteur') ? 'border-color: var(--danger);' : '' }}">
                @error('numeroCompteur')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Type d'Abonnement</label>
                <select name="typeAbonnement" required style="width: 100%; padding: 0.75rem; border-radius: 0.75rem; border: 1px solid {{ $errors->has('typeAbonnement') ? 'var(--danger)' : 'var(--border)' }}; outline: none;">
                    <option value="">Sélectionnez un type</option>
                    <option value="DOMESTIQUE" {{ old('typeAbonnement') == 'DOMESTIQUE' ? 'selected' : '' }}>DOMESTIQUE</option>
                    <option value="PROFESSIONNEL" {{ old('typeAbonnement') == 'PROFESSIONNEL' ? 'selected' : '' }}>PROFESSIONNEL</option>
                </select>
                @error('typeAbonnement')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('web.abonnes.index') }}" class="btn" style="background: #f1f5f9; color: var(--text-main);">Annuler</a>
            <button type="submit" class="btn btn-primary">Enregistrer l'abonné</button>
        </div>
    </form>
</div>
@endsection
