<?php

namespace App\Http\Controllers;

use App\Http\Requests\Abonne\StoreAbonneRequest;
use App\Http\Requests\Web\LoginRequest;
use App\Models\Abonne;
use App\Models\Facture;
use App\Services\AbonneService;
use App\Services\FactureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebController extends Controller
{
    public function __construct(
        private readonly AbonneService $abonneService,
        private readonly FactureService $factureService
    ) {}

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('web.dashboard');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            $request->session()->regenerate();

            return redirect()->intended(route('web.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Mot de passe incorrect.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('web.login');
    }

    public function dashboard()
    {
        // Utilisation des modèles pour les compteurs simples
        $totalAbonnes = Abonne::count();
        $totalFactures = Facture::count();
        $caTotal = Facture::where('statut', 'PAYEE')->sum('montantTotal');

        // Utilisation du service pour récupérer les derniers abonnés
        $recentAbonnes = $this->abonneService->findAll([], 5);

        return view('dashboard', compact('totalAbonnes', 'totalFactures', 'caTotal', 'recentAbonnes'));
    }

    public function abonnesIndex(Request $request)
    {
        $filters = $request->only(['search', 'ville', 'typeAbonnement']);
        $abonnes = $this->abonneService->findAll($filters);

        return view('abonnes.index', compact('abonnes'));
    }

    public function abonnesCreate()
    {
        return view('abonnes.create');
    }

    public function abonnesStore(StoreAbonneRequest $request)
    {
        $this->abonneService->create($request->validated());

        return redirect()->route('web.abonnes.index')->with('success', 'L\'abonné a été créé avec succès.');
    }

    public function facturesIndex(Request $request)
    {
        $filters = $request->only(['statut', 'dateEmission']);
        $factures = $this->factureService->findAll($filters);

        return view('factures.index', compact('factures'));
    }

    public function facturesCreate()
    {
        $abonnes = Abonne::orderBy('nom')->get();

        return view('factures.create', compact('abonnes'));
    }

    public function facturesStore(GenererFactureRequest $request)
    {
        $this->factureService->generate($request->validated());

        return redirect()->route('web.factures.index')->with('success', 'La facture a été générée avec succès.');
    }

    public function facturesShow($id)
    {
        $facture = $this->factureService->findById($id);

        return view('factures.index', ['factures' => collect([$facture])]);
    }
}
