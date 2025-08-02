<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Invoice;
use Carbon\Carbon; 

class DashboardController extends Controller
{
    public function index(Request $request)
{
    $user = $request->user(); // 🔒 Get the authenticated user

    $from = $request->input('from');
    $to = $request->input('to');
    $selectedRange = $request->input('range', 'this_month');

    $baseQuery = Invoice::query()
        ->with('client')
        ->where('user_id', $user->id); // 🔒 Only user's invoices

    if ($from && $to) {
        $startDate = \Carbon\Carbon::parse($from)->startOfDay();
        $endDate = \Carbon\Carbon::parse($to)->endOfDay();
        $baseQuery->whereBetween('date', [$startDate, $endDate]);
    }

    $totalInvoices = (clone $baseQuery)->count();
    $totalCollected = (float) (clone $baseQuery)->where('status', 'paid')->sum('total');
    $totalPending = (float) (clone $baseQuery)->where('status', '!=', 'paid')->sum('total');
    $overdue = (clone $baseQuery)->where('due_date', '<', now())->where('status', '!=', 'paid')->count();

    // Chart
    $monthlyChartData = [];
    $chartLabels = [];

    if ($from && $to) {
        $startDate = \Carbon\Carbon::parse($from)->startOfMonth();
        $endDate = \Carbon\Carbon::parse($to)->endOfMonth();
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $monthlyChartData[$monthKey] = 0;
            $chartLabels[$monthKey] = $currentDate->format('M Y');
            $currentDate->addMonth();
        }

        $monthlyDataRaw = Invoice::selectRaw('DATE_FORMAT(date, "%Y-%m") as month_year, SUM(total) as total')
            ->where('user_id', $user->id) // 🔒 Only user's invoices
            ->whereBetween('date', [$from, $to])
            ->groupBy('month_year')
            ->pluck('total', 'month_year');

        foreach ($monthlyDataRaw as $monthYear => $total) {
            if (isset($monthlyChartData[$monthYear])) {
                $monthlyChartData[$monthYear] = (float) $total;
            }
        }

    } else {
        for ($i = 1; $i <= 12; $i++) {
            $monthKey = now()->year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $monthlyChartData[$monthKey] = 0;
            $chartLabels[$monthKey] = \Carbon\Carbon::create(null, $i, 1)->format('M');
        }

        $monthlyDataRaw = Invoice::selectRaw('MONTH(date) as month, SUM(total) as total')
            ->where('user_id', $user->id) // 🔒 Only user's invoices
            ->whereYear('date', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        foreach ($monthlyDataRaw as $month => $total) {
            $monthKey = now()->year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $monthlyChartData[$monthKey] = (float) $total;
        }
    }

    // Recent invoices
    $recentInvoices = Invoice::where('user_id', $user->id) // 🔒 Only user's invoices
        ->latest()
        ->take(5)
        ->with('client')
        ->get();

    return Inertia::render('Dashboard', [
        'metrics' => [
            'totalInvoices' => $totalInvoices,
            'totalCollected' => $totalCollected,
            'totalPending' => $totalPending,
            'overdue' => $overdue,
        ],
        'monthlyChartData' => array_values($monthlyChartData),
        'monthlyChartLabels' => array_values($chartLabels),
        'recentInvoices' => $recentInvoices,
        'selectedRange' => $selectedRange,
        'initialFromDate' => $from,
        'initialToDate' => $to,
    ]);
}

}