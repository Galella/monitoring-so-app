<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cm;
use App\Models\Coin;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        // Helper closures for join logic (CM + Container)
        $matchCondition = function($query) {
             $query->select(DB::raw(1))
                   ->from('coins')
                   ->whereColumn('coins.container', 'cms.container')
                   ->whereColumn('coins.cm', 'cms.cm');
        };
        
        $reverseMatchCondition = function($query) {
             $query->select(DB::raw(1))
                   ->from('cms')
                   ->whereColumn('coins.container', 'cms.container')
                   ->whereColumn('coins.cm', 'cms.cm');
        };

        // 1. Calculate Counts for Tabs
        // Note: This could be optimized to cached counts if data is large
        $countUnmatchedCm = DB::table('cms')->whereNotExists($matchCondition)->count();
        $countUnmatchedCoin = DB::table('coins')->whereNotExists($reverseMatchCondition)->count();
        $countMatched = DB::table('cms')->whereExists($matchCondition)->count();
        
        // Total count (Union estimation or simple sum of distincts if no overlap, but here overlapped)
        // All = UnmatchedCM + UnmatchedCoin + Matched
        $countAll = $countUnmatchedCm + $countUnmatchedCoin + $countMatched;

        $counts = [
            'all' => $countAll,
            'matched' => $countMatched,
            'unmatched_cm' => $countUnmatchedCm, // Missing Coin
            'unmatched_coin' => $countUnmatchedCoin // Missing CM
        ];

        // 2. Build the Main Query based on Status
        $query = null;

        if ($status === 'matched') {
            // Source: CMS (inner join Coins basically)
            $query = DB::table('cms')
                ->select('cm', 'container', DB::raw("'MATCHED' as status"))
                ->whereExists($matchCondition);
                
        } elseif ($status === 'unmatched_cm') {
            // Missing Coin -> Exists in CM, not in Coin
            $query = DB::table('cms')
                ->select('cm', 'container', DB::raw("'UNMATCHED_CM' as status"))
                ->whereNotExists($matchCondition);
                
        } elseif ($status === 'unmatched_coin') {
            // Missing CM -> Exists in Coin, not in CM
            $query = DB::table('coins')
                ->select('cm', 'container', DB::raw("'UNMATCHED_COIN' as status"))
                ->whereNotExists($reverseMatchCondition);
                
        } else {
            // ALL -> Union strategy
            $cmKeys = DB::table('cms')
                ->select('cm', 'container');
                
            $query = DB::table('coins')
                ->select('cm', 'container')
                ->union($cmKeys);
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
            
            $cmData = Cm::where('container', $key->container)
                        ->where('cm', $key->cm)
                        ->first();
                        
            $coinData = Coin::where('container', $key->container)
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
}
