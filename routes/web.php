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

// TEMPORARY DIAGNOSTIC ROUTE
Route::get('/diagnose-data', function() {
    echo "<pre>";
    echo "\n--- DIAGNOSA USER ---\n";
    $users = App\Models\User::whereNotNull('area_id')->get();
    foreach($users as $u) echo "User: {$u->name} | Role: {$u->role->name} | Area ID: {$u->area_id}\n";

    echo "\n--- DIAGNOSA CM (Data yg tidak terlihat) ---\n";
    $nullCms = App\Models\Cm::whereNull('area_id')->get();
    echo "Jumlah CM tanpa Area: " . $nullCms->count() . "\n";
    foreach($nullCms->take(5) as $cm) echo "Sample CM: {$cm->cm} | ID: {$cm->id}\n";

    echo "\n--- DIAGNOSA COIN (Data yg tidak terlihat) ---\n";
    $nullCoins = App\Models\Coin::whereNull('area_id')->get();
    echo "Jumlah Coin tanpa Area: " . $nullCoins->count() . "\n";
    
    echo "Variasi Stasiun Asal pada data yg NULL:\n";
    $stations = $nullCoins->pluck('stasiun_asal')->unique();
    foreach($stations as $s) echo "- {$s}\n";
    echo "</pre>";
});
