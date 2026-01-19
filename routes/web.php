<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

Route::get('/', function () {
    return view('auth.login');
});

// Route untuk autentikasi bawaan Laravel (dengan Fortify)
// Kita harus menambahkan route login manual karena kita menggunakan layout sendiri

// Route publik untuk login
Route::get('/login', function() {
    return view('auth.login');
})->name('login');

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Route untuk dashboard admin yang dilindungi oleh middleware role
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Route untuk manajemen user
    Route::resource('users', UserController::class)->except(['show']);

    // Route untuk manajemen role
    Route::resource('roles', RoleController::class)->except(['show']);

    // Route untuk manajemen CM Data
    Route::get('cms/template', [\App\Http\Controllers\CmController::class, 'downloadTemplate'])->name('cms.template');
    Route::get('cms/export', [\App\Http\Controllers\CmController::class, 'export'])->name('cms.export');
    Route::post('cms/import', [\App\Http\Controllers\CmController::class, 'import'])->name('cms.import');
    Route::resource('cms', \App\Http\Controllers\CmController::class);

    // Route untuk manajemen Coin Data
    Route::get('coins/template', [\App\Http\Controllers\CoinController::class, 'downloadTemplate'])->name('coins.template');
    Route::get('coins/export', [\App\Http\Controllers\CoinController::class, 'export'])->name('coins.export');
    Route::post('coins/import', [\App\Http\Controllers\CoinController::class, 'import'])->name('coins.import');
    Route::resource('coins', \App\Http\Controllers\CoinController::class)->except(['create', 'store']);

    // Route untuk Monitoring
    Route::get('monitoring', [\App\Http\Controllers\MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/export', [App\Http\Controllers\MonitoringController::class, 'export'])->name('monitoring.export');
    
    // Monitoring SO
    Route::get('/monitoring-so', [App\Http\Controllers\MonitoringSoController::class, 'index'])->name('monitoring-so.index');
    Route::get('/monitoring-so/export', [App\Http\Controllers\MonitoringSoController::class, 'export'])->name('monitoring-so.export');
    Route::put('/monitoring-so/{id}', [App\Http\Controllers\MonitoringSoController::class, 'update'])->name('monitoring-so.update');
    Route::post('/monitoring-so/bulk-update', [App\Http\Controllers\MonitoringSoController::class, 'bulkUpdate'])->name('monitoring-so.bulk-update');
    
    // Timeline PO Routes
    Route::get('/monitoring-so/timeline', [App\Http\Controllers\PoTimelineController::class, 'index'])->name('po-timeline.index');
    Route::post('/monitoring-so/timeline', [App\Http\Controllers\PoTimelineController::class, 'store'])->name('po-timeline.store');

    // Route untuk Wilayah dan Area
    Route::resource('wilayahs', \App\Http\Controllers\WilayahController::class);
    Route::resource('areas', \App\Http\Controllers\AreaController::class);

    // Activity Logs
    Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
    // Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/monitoring-so', [App\Http\Controllers\ReportMonitoringSoController::class, 'index'])->name('monitoring-so.index');
        Route::get('/monitoring-so/export', [App\Http\Controllers\ReportMonitoringSoController::class, 'export'])->name('monitoring-so.export');
        Route::get('/monitoring-so/export-pdf', [App\Http\Controllers\ReportMonitoringSoController::class, 'exportPdf'])->name('monitoring-so.export-pdf');
    });

});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

// Route untuk user biasa
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');
});

// TEMPORARY REPAIR & DIAGNOSTIC ROUTE
Route::get('/diagnose-data', function() {
    echo "<pre>";
    echo "=== MULAI PERBAIKAN DATA ===\n";

    // 1. FIX COINS (Area NULL)
    $coinsKlari = App\Models\Coin::whereNull('area_id')
        ->where('stasiun_asal', 'like', '%Klari%')
        ->update(['area_id' => 2, 'wilayah_id' => 1]);
    echo "Fixed Coins Klari: $coinsKlari\n";

    $coinsSao = App\Models\Coin::whereNull('area_id')
        ->where('stasiun_asal', 'like', '%Sungai Lagoa%')
        ->update(['area_id' => 1, 'wilayah_id' => 1]);
    echo "Fixed Coins Sungai Lagoa: $coinsSao\n";

    $coinsJict = App\Models\Coin::whereNull('area_id')
        ->where(function($q) {
             $q->where('stasiun_asal', 'like', '%JICT%')
               ->orWhere('stasiun_asal', 'like', '%Jakarta International Container Terminal%');
        })
        ->update(['area_id' => 3, 'wilayah_id' => 1]);
    echo "Fixed Coins JICT: $coinsJict\n";

    // 2. FIX CM (Validation only, ensure 'KLI' is in Area 2)
    // Update any record with 'KLI' in CM code that is NOT in Area 2
    $cmsFixed = App\Models\Cm::where('cm', 'like', '%KLI%')
        ->where(function($q) {
            $q->whereNull('area_id')
              ->orWhere('area_id', '!=', 2);
        })
        ->update(['area_id' => 2, 'wilayah_id' => 1]);
    echo "Fixed Misassigned CMs for Klari: $cmsFixed\n";

    echo "\n=== HASIL AKHIR PADA DATABASE ===\n";
    $stats = App\Models\Coin::selectRaw('area_id, count(*) as total')->groupBy('area_id')->get();
    foreach($stats as $s) {
        $name = match($s->area_id) {
            1 => 'Sungai Lagoa', 2 => 'Klari', 3 => 'JICT', null => 'NULL', default => 'Other'
        };
        echo "Area $s->area_id ($name): $s->total Records\n";
    }

    echo "\n=== CHECK USER VISIBILITY ===\n";
    $klariUser = App\Models\User::where('area_id', 2)->first();
    if($klariUser) {
        echo "Admin Klari sees: " . App\Models\Coin::forUser($klariUser)->count() . " records.\n";
    }
    
    echo "</pre>";
});
