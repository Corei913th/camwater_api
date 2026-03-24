<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Abonne;
use App\Models\User;
use App\Models\Reclamation;
use Illuminate\Http\Response;

class MetricsController extends Controller
{
    /**
     * Expose business metrics for Prometheus.
     */
    public function index(): Response
    {
        $metrics = [];

        // 1. Total Invoices
        $invoicesTotal = Facture::count();
        $metrics[] = "# HELP camwater_invoices_total Total number of invoices generated";
        $metrics[] = "# TYPE camwater_invoices_total counter";
        $metrics[] = "camwater_invoices_total $invoicesTotal";

        // 2. Total Revenue
        $revenueTotal = Facture::sum('montantTotal');
        $metrics[] = "# HELP camwater_revenue_total Total revenue generated from invoices";
        $metrics[] = "# TYPE camwater_revenue_total counter";
        $metrics[] = "camwater_revenue_total $revenueTotal";

        // 3. Active Subscribers (Abonnes)
        $abonnesTotal = Abonne::count();
        $metrics[] = "# HELP camwater_active_subscribers Total number of active subscribers";
        $metrics[] = "# TYPE camwater_active_subscribers gauge";
        $metrics[] = "camwater_active_subscribers $abonnesTotal";

        // 4. Pending Complaints
        $complaintsPending = Reclamation::where('statut', '!=', 'traite')->count();
        $metrics[] = "# HELP camwater_complaints_pending Total number of pending complaints";
        $metrics[] = "# TYPE camwater_complaints_pending gauge";
        $metrics[] = "camwater_complaints_pending $complaintsPending";

        return response(implode("\n", $metrics) . "\n", 200)
            ->header('Content-Type', 'text/plain; version=0.0.4');
    }
}
