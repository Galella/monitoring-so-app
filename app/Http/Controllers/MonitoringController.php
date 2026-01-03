<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cm;
use App\Models\Coin;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonitoringExport;
use App\Services\DataScopeService;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        // Helper closures removed, using DataScopeService instead

        // 1. Calculate Counts for Tabs
        $qUnmatchedCm = DB::table('cms')->whereNotExists(DataScopeService::matchCondition());
        DataScopeService::apply($qUnmatchedCm, 'cms');
        $countUnmatchedCm = $qUnmatchedCm->count();

        $qUnmatchedCoin = DB::table('coins')->whereNotExists(DataScopeService::reverseMatchCondition());
        DataScopeService::apply($qUnmatchedCoin, 'coins');
        $countUnmatchedCoin = $qUnmatchedCoin->count();

        $qMatched = DB::table('cms')->whereExists(DataScopeService::matchCondition());
        DataScopeService::apply($qMatched, 'cms');
        $countMatched = $qMatched->count();
        
        $countAll = $countUnmatchedCm + $countUnmatchedCoin + $countMatched;

        $counts = [
            'all' => $countAll,
            'matched' => $countMatched,
            'unmatched_cm' => $countUnmatchedCm, 
            'unmatched_coin' => $countUnmatchedCoin 
        ];

        // 2. Build the Main Query based on Status
        $query = null;

        if ($status === 'matched') {
            $query = DB::table('cms')
                ->select('cm', 'container', DB::raw("'MATCHED' as status"))
                ->whereExists(DataScopeService::matchCondition());
            DataScopeService::apply($query, 'cms');
                
        } elseif ($status === 'unmatched_cm') {
            $query = DB::table('cms')
                ->select('cm', 'container', DB::raw("'UNMATCHED_CM' as status"))
                ->whereNotExists(DataScopeService::matchCondition());
            DataScopeService::apply($query, 'cms');
                
        } elseif ($status === 'unmatched_coin') {
            $query = DB::table('coins')
                ->select('cm', 'container', DB::raw("'UNMATCHED_COIN' as status"))
                ->whereNotExists(DataScopeService::reverseMatchCondition());
            DataScopeService::apply($query, 'coins');
                
        } else {
            // ALL -> Union strategy
            $cmKeys = DB::table('cms')->select('cm', 'container');
            DataScopeService::apply($cmKeys, 'cms');
                
            $coinKeys = DB::table('coins')->select('cm', 'container');
            DataScopeService::apply($coinKeys, 'coins');

            $query = $coinKeys->union($cmKeys);
        }

        // 3. Apply Search
        // Since we are building a base query for Keys, we wrap it to search
        $finalQuery = DB::table(DB::raw("({$query->toSql()}) as keys_table"))
            ->mergeBindings($query); // Important for bound parameters

        if ($search) {
            $finalQuery->where(function($q) use ($search) {
                $q->where('container', 'like', "%{$search}%")
                  ->orWhere('cm', 'like', "%{$search}%");
            });
        }

        // 4. Paginate Keys
        $paginatedKeys = $finalQuery->paginate(10);

        // 5. Hydrate Data
        // Optimization: Eager load or fetch in batch?
        // Given logical separation, we'll just fetch individually for simplicity for now,
        // or loop through.
        
        $items = collect($paginatedKeys->items())->map(function($key) use ($status) {
            // If we already know the status from the query (except 'all'), use it.
            // For 'all', we calculate it.
            
            $computedStatus = $key->status ?? null;
            
            $cmData = Cm::forUser(auth()->user())
                        ->where('container', $key->container)
                        ->where('cm', $key->cm)
                        ->first();
                        
            $coinData = Coin::forUser(auth()->user())
                            ->where('container', $key->container)
                            ->where('cm', $key->cm)
                            ->first();

            if (!$computedStatus) {
                if ($cmData && $coinData) $computedStatus = 'MATCHED';
                elseif ($cmData && !$coinData) $computedStatus = 'UNMATCHED_CM'; // Missing Coin
                elseif (!$cmData && $coinData) $computedStatus = 'UNMATCHED_COIN'; // Missing CM
            }

            return (object) [
                'key_cm' => $key->cm,
                'key_container' => $key->container,
                'cm_data' => $cmData,
                'coin_data' => $coinData,
                'status' => $computedStatus
            ];
        });

        return view('monitoring.index', [
            'items' => $items,
            'paginator' => $paginatedKeys,
            'currentStatus' => $status,
            'counts' => $counts
        ]);
    }

    public function export(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        $user = auth()->user();

        return Excel::download(new MonitoringExport($status, $search, $user), 'monitoring_data_' . $status . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
}
