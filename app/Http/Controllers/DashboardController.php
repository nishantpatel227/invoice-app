<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Invoice;
use Carbon\Carbon; // Use Carbon for date manipulation

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get date filter parameters from the request
        $from = $request->input('from');
        $to = $request->input('to');
        $selectedRange = $request->input('range', 'this_month'); // Get the selected range to persist

        // --- Base Query for filtering ---
        // This base query will be cloned for each metric if it needs the date filter
        $baseQuery = Invoice::query();
        if ($from && $to) {
            // Ensure Carbon parsing for robust date handling (especially for the 'to' end of range)
            $startDate = Carbon::parse($from)->startOfDay();
            $endDate = Carbon::parse($to)->endOfDay();
            $baseQuery->whereBetween('date', [$startDate, $endDate]);
        }

        // --- Metrics Calculations ---
        // Clone the base query for each metric to apply specific conditions
        $totalInvoices = (clone $baseQuery)->count();

        $totalCollected = (float) (clone $baseQuery)->where('status', 'paid')->sum('total');
        $totalPending = (float) (clone $baseQuery)->where('status', '!=', 'paid')->sum('total');

        // For overdue, ensure the date comparison is done on the full timestamp
        $overdue = (clone $baseQuery)->where('due_date', '<', now())->where('status', '!=', 'paid')->count();


        // --- Monthly Chart Data ---
        $monthlyChartData = [];
        $chartLabels = [];

        // Determine the period for the chart based on the 'from' and 'to' dates
        if ($from && $to) {
            $startDate = Carbon::parse($from);
            $endDate = Carbon::parse($to);

            $currentDate = clone $startDate->startOfMonth(); // Start from the beginning of the first month
            while ($currentDate <= $endDate->endOfMonth()) { // Loop until the end of the last month
                $monthKey = $currentDate->format('Y-m'); // e.g., '2025-07'
                $monthlyChartData[$monthKey] = 0; // Initialize with 0
                $chartLabels[$monthKey] = $currentDate->format('M Y'); // e.g., 'Jul 2025'
                $currentDate->addMonth();
            }

            // Fetch actual data for the selected period
            $monthlyDataRaw = Invoice::selectRaw('DATE_FORMAT(date, "%Y-%m") as month_year, SUM(total) as total')
                ->whereBetween('date', [$from, $to]) // Use original string dates for query
                ->groupBy('month_year')
                ->pluck('total', 'month_year');

            foreach ($monthlyDataRaw as $monthYear => $total) {
                if (isset($monthlyChartData[$monthYear])) {
                    $monthlyChartData[$monthYear] = (float) $total;
                }
            }

            // Sort labels and data by date to ensure correct order
            uksort($monthlyChartData, function($a, $b) {
                return strtotime($a . '-01') - strtotime($b . '-01');
            });
            uksort($chartLabels, function($a, $b) {
                return strtotime($a . '-01') - strtotime($b . '-01');
            });

        } else {
            // Default to current year's monthly data if no specific range is selected
            for ($i = 1; $i <= 12; $i++) {
                $monthKey = now()->year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $monthlyChartData[$monthKey] = 0;
                $chartLabels[$monthKey] = Carbon::create(null, $i, 1)->format('M');
            }

            $monthlyDataRaw = Invoice::selectRaw('MONTH(date) as month, SUM(total) as total')
                ->whereYear('date', now()->year)
                ->groupBy('month')
                ->pluck('total', 'month');

            foreach ($monthlyDataRaw as $month => $total) {
                $monthKey = now()->year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
                $monthlyChartData[$monthKey] = (float) $total;
            }
        }

        // --- Recent Invoices ---
        // Decide if recent invoices should also be filtered by the date range.
        // Usually, 'recent' means across all time, so keeping it unfiltered.
        // If you want them filtered by the $from/$to, use `(clone $baseQuery)->latest()->take(5)->with('client')->get()`.
        $recentInvoices = Invoice::latest()->take(5)->with('client')->get();


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
            'selectedRange' => $selectedRange, // Pass back to Vue for dropdown state
            'initialFromDate' => $from,       // Pass back for custom date input state
            'initialToDate' => $to,           // Pass back for custom date input state
        ]);
    }
}