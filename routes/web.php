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
});

// Route untuk user biasa
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');
});
