<?php

use App\Http\Controllers\Admin\AircraftController;
use App\Http\Controllers\Admin\AirlineController;
use App\Http\Controllers\Admin\AirportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GateController;
use App\Http\Controllers\Admin\RunwayController;
use App\Http\Controllers\Admin\TerminalController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('airlines', AirlineController::class);
        Route::resource('airports', AirportController::class);
        Route::resource('aircraft', AircraftController::class);
        Route::resource('terminals', TerminalController::class);
        Route::resource('gates', GateController::class);
        Route::resource('runways', RunwayController::class);
    });

require __DIR__.'/auth.php';
