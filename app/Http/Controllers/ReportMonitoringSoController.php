<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coin;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\MonitoringSoReportExport; // Placeholder for now

class ReportMonitoringSoController extends Controller
{
    public function index(Request $request)
    {
        // Filter Inputs
        $dateType = $request->get('date_type', 'atd'); // atd or submit_so
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $customer = $request->get('customer');
        $status = $request->get('status');
        $stasiunAsal = $request->get('stasiun_asal');
        $stasiunTujuan = $request->get('stasiun_tujuan');
        
        // Base Query
        $query = Coin::query()
            ->select('coins.*');

        // Apply Scope
        $query->forUser(auth()->user());

        // Apply Filters
        $this->applyFilters($query, $request);

        // Get Distinct Values for Dropdowns (Scoped to user ideally, but global is faster for now)
        // We can scope them to be safe
        $customers = Coin::forUser(auth()->user())->distinct()->pluck('customer')->filter()->sort();
        $stasiunAsals = Coin::forUser(auth()->user())->distinct()->pluck('stasiun_asal')->filter()->sort();
        $stasiunTujuans = Coin::forUser(auth()->user())->distinct()->pluck('stasiun_tujuan')->filter()->sort();

        // Clone query for aggregation (before pagination)
        // Note: $query already has select('coins.*'), but we will override it in aggregate query or use a fresh query with same wheres
        // Ideally we should have built the query filters on a base object then cloned.
        // For simplicity, we'll re-apply limits or use specific logic. 
        // Better: Grab the visible POs from the paginated result and query stats for them.
        
        $items = $query->orderBy('customer', 'asc')
                       ->orderBy('po', 'asc')
                       ->paginate(20)->withQueryString();

        // Calculate Aggregates for Visible POs
        // We want to sum nominal_ppn and count rows for each PO in the current page.
        // Since we are iterating $items, we can just aggregate from the collection itself!
        // No need for extra DB query if we just want stats for the *displayed* rows.
        // Check if user meant TOTAL for the PO regardless of pagination? 
        // Usually grouping headers imply summary of the group. If the group is split across pages, usually you show summary of *that page* or *total*.
        // Let's assume summary of the *whole* PO (filtered). That requires a DB query.
        
        $visiblePos = $items->pluck('po')->unique()->filter();
        $visibleCustomers = $items->pluck('customer')->unique()->filter();
        
        $poStats = [];
        $customerStats = [];

        // 1. PO Stats
        if ($visiblePos->isNotEmpty()) {
            $statsQuery = Coin::query()->whereIn('po', $visiblePos);
            $this->applyFilters($statsQuery, $request); // Helper method would be nice, but inline for now
            $statsQuery->forUser(auth()->user());
            
            $poStats = $statsQuery->selectRaw('po, count(*) as count_container, sum(nominal_ppn) as sum_ppn')
                                 ->groupBy('po')
                                 ->get()
                                 ->keyBy('po');
        }

        // 2. Customer Stats
        if ($visibleCustomers->isNotEmpty()) {
            $custStatsQuery = Coin::query()->whereIn('customer', $visibleCustomers);
            $this->applyFilters($custStatsQuery, $request); // We need to duplicate filter logic or refactor
            $custStatsQuery->forUser(auth()->user());

            $customerStats = $custStatsQuery->selectRaw('customer, count(*) as count_container, sum(nominal_ppn) as sum_ppn')
                                            ->groupBy('customer')
                                            ->get()
                                            ->keyBy('customer');
        }

        return view('reports.monitoring_so.index', compact(
            'items', 
            'customers', 
            'stasiunAsals', 
            'stasiunTujuans',
            'startDate',
            'endDate',
            'dateType',
            'customer',
            'status',
            'stasiunAsal',
            'stasiunTujuan',
            'poStats',
            'customerStats'
        ));
    }

    // Helper to avoid Code Duplication
    private function applyFilters($query, $request)
    {
        $dateType = $request->get('date_type', 'atd');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $customer = $request->get('customer');
        $status = $request->get('status');
        $stasiunAsal = $request->get('stasiun_asal');
        $stasiunTujuan = $request->get('stasiun_tujuan');

        if ($startDate && $endDate) {
            $column = $dateType === 'submit_so' ? 'coins.submit_so' : 'coins.atd';
            $query->whereBetween($column, [$startDate, $endDate]);
        }
        if ($customer) {
            $query->where('coins.customer', $customer);
        }
        if ($stasiunAsal) {
            $query->where('coins.stasiun_asal', $stasiunAsal);
        }
        if ($stasiunTujuan) {
             $query->where('coins.stasiun_tujuan', $stasiunTujuan);
        }
         
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $numericPattern = $isSqlite ? '[0-9]*' : '^[0-9]+$';
        $numericOperator = $isSqlite ? 'GLOB' : 'REGEXP';

         if ($status === 'system') {
            $query->where('coins.so', $numericOperator, $numericPattern);
        } elseif ($status === 'manual') {
             $query->where('coins.so', 'LIKE', '%Manual%');
        } elseif ($status === 'not_submitted') {
             $query->where(function($q) {
                $q->whereNull('coins.so')
                  ->orWhere('coins.so', '')
                  ->orWhere('coins.so', 'Not Submitted');
            });
        }
    }


    public function export(Request $request)
    {
        // Reuse same logic for export (later)
        // return Excel::download(new MonitoringSoReportExport($request), 'report_monitoring_so.xlsx');
        return redirect()->back()->with('warning', 'Fitur export belum diaktifkan.');
    }

    public function exportPdf(Request $request)
    {
        $query = Coin::query()->select('coins.*');
        $query->forUser(auth()->user());
        $this->applyFilters($query, $request);

        // Get all items (no pagination for PDF)
        // Memory warning: high volume data might crash this.
        $items = $query->orderBy('customer', 'asc')
                       ->orderBy('po', 'asc')
                       ->get();

        // Calculate stats on the collection
        $poStats = $items->groupBy('po')->map(function ($rows) {
             return (object)[
                 'count_container' => $rows->count(),
                 'sum_ppn' => $rows->sum('nominal_ppn'),
             ];
        });

        $customerStats = $items->groupBy('customer')->map(function ($rows) {
             return (object)[
                 'count_container' => $rows->count(),
                 'sum_ppn' => $rows->sum('nominal_ppn'),
             ];
        });

        $pdf = Pdf::loadView('reports.monitoring_so.pdf', compact('items', 'poStats', 'customerStats'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('laporan-monitoring-so.pdf');
    }
}
