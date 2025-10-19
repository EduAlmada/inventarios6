<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NotaController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\DepositoController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\TipoActividadController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\IsAdmin;


Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::middleware([IsAdmin::class])->group(function () {
    Route::resource('admin/users', UserController::class)->names('admin.users');
    Route::resource('tipos', TipoActividadController::class);
    Route::get('articulos/download', [ArticuloController::class, 'download'])->name('articulos.download');
    Route::resource('articulos', ArticuloController::class);
    Route::resource('depositos', DepositoController::class);
    Route::resource('zonas', ZonaController::class);
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::resource('stocks', StockController::class)->except(['index']); 
    Route::resource('movimientos', MovimientoController::class);
    
    Route::resource('notas', NotaController::class)->except(['index', 'show']);
});

Route::middleware('auth')->group(function () {
    Route::get('actividades/download', [ActividadController::class, 'download'])->name('actividades.download');
    Route::resource('actividades', ActividadController::class)->parameters(['actividades' => 'actividad',]);
    Route::resource('notas', NotaController::class)->only(['index', 'show']);

});