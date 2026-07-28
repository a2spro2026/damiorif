<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/fournisseurs', [PageController::class, 'fournisseurs'])->name('fournisseurs.index');
    Route::get('/stock', [PageController::class, 'stock'])->name('stock.index');
    Route::get('/clients', [PageController::class, 'clients'])->name('clients.index');
    Route::get('/charges', [PageController::class, 'charges'])->name('charges.index');
    Route::get('/rapports', [PageController::class, 'rapports'])->name('rapports.index');
    Route::get('/configuration', [PageController::class, 'configuration'])->name('configuration.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
