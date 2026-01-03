<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coin;
use App\Models\Cm;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonitoringSoExport;

class MonitoringSoController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'not_submitted');
        $search = $request->get('search');

        // Base Query: Matched Records (Coins that have matching CM)
        // We start from Coin because it holds the 'so' column.
        $query = Coin::query()
            ->join('cms', function($join) {
                $join->on('coins.container', '=', 'cms.container')
                     ->on('coins.cm', '=', 'cms.cm');
            })
            ->select('coins.*', 'cms.seal as cm_seal', 'cms.shipper as cm_shipper', 'cms.consignee as cm_consignee');

        // Apply Data Scoping (Location)
        $query->forUser(auth()->user());

        // Calculate Counts for Tabs using separate queries (efficiency note: could be single query with conditional counts)
        // Clone query for counts to preserve joins and scopes
        $baseCountQuery = clone $query;
        
        // Check regex capability based on driver
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $numericPattern = $isSqlite ? '[0-9]*' : '^[0-9]+$';
        $numericOperator = $isSqlite ? 'GLOB' : 'REGEXP';

        $countSystem = (clone $baseCountQuery)->where('coins.so', $numericOperator, $numericPattern)->count();
        $countManual = (clone $baseCountQuery)->where('coins.so', 'LIKE', '%Manual%')->count();
        $countNotSubmitted = (clone $baseCountQuery)->where(function($q) {
            $q->whereNull('coins.so')
              ->orWhere('coins.so', '')
              ->orWhere('coins.so', 'Not Submitted');
        })->count();

        $counts = [
            'system' => $countSystem,
            'manual' => $countManual,
            'not_submitted' => $countNotSubmitted
        ];

        // Apply Status Filter
        if ($status === 'system') {
            $query->where('coins.so', $numericOperator, $numericPattern);
        } elseif ($status === 'manual') {
            $query->where('coins.so', 'LIKE', '%Manual%');
        } else {
            // Default: Not Submitted
            $query->where(function($q) {
                $q->whereNull('coins.so')
                  ->orWhere('coins.so', '')
                  ->orWhere('coins.so', 'Not Submitted');
            });
        }

        // Apply Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('coins.container', 'like', "%{$search}%")
                  ->orWhere('coins.cm', 'like', "%{$search}%")
                  ->orWhere('coins.so', 'like', "%{$search}%");
            });
        }

        // Order by PO for grouping
        $query->orderBy('coins.po', 'asc');

        $items = $query->paginate(10)->withQueryString();

        return view('monitoring_so.index', compact('items', 'counts', 'status'));
    }

    public function update(Request $request, $id)
    {
        $coin = Coin::findOrFail($id);
        
        // Basic validation
        $request->validate([
            'so' => 'required|string|max:255',
            'submit_so' => 'required|date',
        ]);

        $coin->update([
            'so' => $request->so,
            'submit_so' => $request->submit_so
        ]);

        return redirect()->back()->with('success', 'SO updated successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:coins,id',
        ]);

        $coins = Coin::whereIn('id', $request->ids)->get();

        foreach ($coins as $coin) {
            $coin->update([
                'so' => 'Manual',
                'submit_so' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Selected records updated to Manual SO.');
    }

    public function export(Request $request)
    {
        $status = $request->get('status', 'not_submitted');
        $search = $request->get('search');
        $user = auth()->user();

        return Excel::download(new MonitoringSoExport($status, $search, $user), 'monitoring_so_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
}
