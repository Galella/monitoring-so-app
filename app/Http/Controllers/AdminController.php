<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DataScopeService;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Data Reconciliation Stats
        $qUnmatchedCm = \Illuminate\Support\Facades\DB::table('cms')->whereNotExists(DataScopeService::matchCondition());
        DataScopeService::apply($qUnmatchedCm, 'cms');
        $countUnmatchedCm = $qUnmatchedCm->count();

        $qUnmatchedCoin = \Illuminate\Support\Facades\DB::table('coins')->whereNotExists(DataScopeService::reverseMatchCondition());
        DataScopeService::apply($qUnmatchedCoin, 'coins');
        $countUnmatchedCoin = $qUnmatchedCoin->count();

        $qMatched = \Illuminate\Support\Facades\DB::table('cms')->whereExists(DataScopeService::matchCondition());
        DataScopeService::apply($qMatched, 'cms');
        $countMatched = $qMatched->count();

        // 2. Monitoring SO Stats (Matched Records Only)
        // We query from Coins joined with CMs to ensure they are matched records
        $baseSoQuery = \App\Models\Coin::query()
            ->join('cms', function($join) {
                $join->on('coins.container', '=', 'cms.container')
                     ->on('coins.cm', '=', 'cms.cm');
            });
        
        // Scope logic for Coin model (using simpler scope call if model traits were perfect, but reusing raw logic for consistency in controller)
        // Actually, let's use the Model scope for cleaner code since we are using Eloquent builder here
        $baseSoQuery->forUser(auth()->user());

        $isSqlite = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite';
        $numericPattern = $isSqlite ? '[0-9]*' : '^[0-9]+$';
        $numericOperator = $isSqlite ? 'GLOB' : 'REGEXP';

        // Clone queries
        $countSoSystem = (clone $baseSoQuery)->where('coins.so', $numericOperator, $numericPattern)->count();
        $countSoManual = (clone $baseSoQuery)->where('coins.so', 'LIKE', '%Manual%')->count();
        $countSoPending = (clone $baseSoQuery)->where(function($q) {
            $q->whereNull('coins.so')
              ->orWhere('coins.so', '')
              ->orWhere('coins.so', 'Not Submitted');
        })->count();

        $stats = [
            'matched' => $countMatched,
            'unmatched_cm' => $countUnmatchedCm,
            'unmatched_coin' => $countUnmatchedCoin,
            'so_system' => $countSoSystem,
            'so_manual' => $countSoManual,
            'so_pending' => $countSoPending,
        ];

        // 3. Transactions Volume by Origin Station
        // 3. Transactions Volume by Train per Month
        // Get data for the last 6 months
        $startDate = now()->subMonths(6)->startOfMonth();
        
        $volumeData = \App\Models\Coin::query()
            ->forUser(auth()->user())
            ->select(
                \Illuminate\Support\Facades\DB::raw("DATE_FORMAT(atd, '%Y-%m') as month_year"),
                'kereta',
                \Illuminate\Support\Facades\DB::raw('count(*) as total')
            )
            ->whereNotNull('atd')
            ->whereNotNull('kereta')
            ->where('kereta', '!=', '')
            ->where('atd', '>=', $startDate)
            ->groupBy('month_year', 'kereta')
            ->orderBy('month_year')
            ->get();

        // Process data for Chart.js
        $months = [];
        $trains = [];
        $matrix = []; // [month][train] = total

        foreach ($volumeData as $row) {
             $m = \Carbon\Carbon::createFromFormat('Y-m', $row->month_year)->format('M Y');
             $t = $row->kereta;
             
             if (!in_array($m, $months)) $months[] = $m;
             if (!in_array($t, $trains)) $trains[] = $t;
             
             $matrix[$m][$t] = $row->total;
        }

        $datasets = [];
        // Define some colors
        $colors = ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', '#605ca8', '#ff851b', '#39cccc', '#D81B60'];
        
        foreach ($trains as $index => $train) {
            $dataPoints = [];
            foreach ($months as $month) {
                $dataPoints[] = $matrix[$month][$train] ?? 0;
            }
            
            $datasets[] = [
                'label' => $train,
                'backgroundColor' => $colors[$index % count($colors)],
                'data' => $dataPoints
            ];
        }

        $stats['volume_labels'] = $months;
        $stats['volume_datasets'] = $datasets;

        return view('admin.dashboard', compact('stats'));
    }
}
