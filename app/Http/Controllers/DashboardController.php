<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Invoice;
use App\Models\Client; // Assuming you have this model

class DashboardController extends Controller
{
    public function index()
    {
        $totalInvoices = Invoice::count();

        // Ensure sum returns 0 if no records are found, or cast to float
        $totalCollected = (float) Invoice::where('status', 'paid')->sum('total');
        $totalPending = (float) Invoice::where('status', '!=', 'paid')->sum('total');

        $overdue = Invoice::where('due_date', '<', now())->where('status', '!=', 'paid')->count();

        $monthlyDataRaw = Invoice::selectRaw('MONTH(date) as month, SUM(total) as total')
            ->whereYear('date', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyChartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyChartData[$i] = 0;
        }

        foreach ($monthlyDataRaw as $month => $total) {
            $monthlyChartData[$month] = (float) $total;
        }

        return Inertia::render('Dashboard', [
            'metrics' => [
                'totalInvoices' => $totalInvoices,
                'totalCollected' => $totalCollected, // Now guaranteed to be a float/number
                'totalPending' => $totalPending,     // Now guaranteed to be a float/number
                'overdue' => $overdue,
            ],
            'monthlyChart' => $monthlyChartData,
            'recentInvoices' => Invoice::latest()->take(5)->with('client')->get(),
        ]);
    }
}