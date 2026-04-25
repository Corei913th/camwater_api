<?php

namespace App\Http\Controllers;

use App\Models\Abonne;
use App\Models\Facture;
use App\Models\Reclamation;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class MetricsController extends Controller
{
    /**
     * Expose business and operational metrics for Prometheus.
     */
    public function index(): Response
    {
        $metrics = [];

        // --- SECTION 1: ABONNES (CROISSANCE) ---
        $abonnesTotal = Abonne::count();
        $abonnesToday = Abonne::whereDate('created_at', Carbon::today())->count();

        $metrics[] = '# HELP camwater_abonnes_total Total number of subscribers';
        $metrics[] = '# TYPE camwater_abonnes_total gauge';
        $metrics[] = "camwater_abonnes_total $abonnesTotal";

        $metrics[] = '# HELP camwater_abonnes_new_today New subscribers registered today';
        $metrics[] = '# TYPE camwater_abonnes_new_today gauge';
        $metrics[] = "camwater_abonnes_new_today $abonnesToday";

        // --- SECTION 2: FACTURATION & FINANCES ---
        $facturesPaidCount = Facture::where('statut', 'PAYEE')->count();
        $facturesUnpaidCount = Facture::where('statut', '!=', 'PAYEE')->count();

        $revenueCollected = Facture::where('statut', 'PAYEE')->sum('montantTotal');
        $revenuePending = Facture::where('statut', '!=', 'PAYEE')->sum('montantTotal');

        $metrics[] = '# HELP camwater_factures_count Number of invoices by status';
        $metrics[] = '# TYPE camwater_factures_count gauge';
        $metrics[] = 'camwater_factures_count{status="paid"} '.$facturesPaidCount;
        $metrics[] = 'camwater_factures_count{status="unpaid"} '.$facturesUnpaidCount;

        $metrics[] = '# HELP camwater_revenue_total Revenue in XAF by status';
        $metrics[] = '# TYPE camwater_revenue_total counter';
        $metrics[] = 'camwater_revenue_total{status="collected"} '.$revenueCollected;
        $metrics[] = 'camwater_revenue_total{status="pending"} '.$revenuePending;

        // --- SECTION 3: CONSOMMATION RESEAU ---
        $totalConsommation = Facture::sum('consommation');
        $avgConsommation = Facture::count() > 0 ? Facture::avg('consommation') : 0;

        $metrics[] = '# HELP camwater_consommation_total_m3 Total water consumption in cubic meters';
        $metrics[] = '# TYPE camwater_consommation_total_m3 counter';
        $metrics[] = "camwater_consommation_total_m3 $totalConsommation";

        $metrics[] = '# HELP camwater_consommation_avg_m3 Average consumption per invoice';
        $metrics[] = '# TYPE camwater_consommation_avg_m3 gauge';
        $metrics[] = "camwater_consommation_avg_m3 $avgConsommation";

        // --- SECTION 4: SERVICE CLIENT ---
        $reclamationsOpen = Reclamation::where('statut', 'PENDING')->count();
        $reclamationsResolved = Reclamation::whereIn('statut', ['APPROUVEE', 'REJETTEE'])->count();

        $metrics[] = '# HELP camwater_reclamations_status Complaints by status';
        $metrics[] = '# TYPE camwater_reclamations_status gauge';
        $metrics[] = 'camwater_reclamations_status{status="open"} '.$reclamationsOpen;
        $metrics[] = 'camwater_reclamations_status{status="resolved"} '.$reclamationsResolved;

        // --- SECTION 5: APPLICATION PERFORMANCE ---
        $durations = Cache::get('app_request_durations', []);
        $avgResponseTime = count($durations) > 0 ? array_sum($durations) / count($durations) : 0;

        $metrics[] = '# HELP camwater_app_response_time_avg Average application response time in seconds';
        $metrics[] = '# TYPE camwater_app_response_time_avg gauge';
        $metrics[] = "camwater_app_response_time_avg $avgResponseTime";

        return response(implode("\n", $metrics)."\n", 200)
            ->header('Content-Type', 'text/plain; version=0.0.4');
    }
}
