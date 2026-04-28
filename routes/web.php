<?php

use App\Http\Controllers\BedGridController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::get('bed-grid', [BedGridController::class, 'index'])->name('bed-grid');
    Route::get('tenants', [TenantController::class, 'index'])->name('tenants');
    Route::get('finance', [FinanceController::class, 'index'])->name('finance');
    
    Route::get('calendar-settings', [App\Http\Controllers\CalendarSettingsController::class, 'index'])->name('calendar.index');
    Route::post('calendar-settings', [App\Http\Controllers\CalendarSettingsController::class, 'store'])->name('calendar.store');
    Route::put('calendar-settings/{calendarMap}', [App\Http\Controllers\CalendarSettingsController::class, 'update'])->name('calendar.update');
});

require __DIR__.'/settings.php';
