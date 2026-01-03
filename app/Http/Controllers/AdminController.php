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

        return view('admin.dashboard', compact('stats'));
    }
}
