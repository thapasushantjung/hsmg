<?php

use App\Http\Controllers\BedActionController;
use App\Http\Controllers\BedGridController;
use App\Http\Controllers\CalendarSettingsController;
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
    Route::get('tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('tenants/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
    Route::get('finance', [FinanceController::class, 'index'])->name('finance');

    // Bed Actions & APIs
    Route::get('api/tenants/search', [TenantController::class, 'search'])->name('api.tenants.search');
    Route::get('api/beds/available', [BedActionController::class, 'available'])->name('api.beds.available');
    Route::post('beds/{bed}/assign', [BedActionController::class, 'assign'])->name('beds.assign');
    Route::post('beds/{bed}/checkout', [BedActionController::class, 'checkout'])->name('beds.checkout');
    Route::post('beds/{bed}/transfer', [BedActionController::class, 'transfer'])->name('beds.transfer');

    Route::get('calendar-settings', [CalendarSettingsController::class, 'index'])->name('calendar.index');
    Route::post('calendar-settings', [CalendarSettingsController::class, 'store'])->name('calendar.store');
    Route::put('calendar-settings/{calendarMap}', [CalendarSettingsController::class, 'update'])->name('calendar.update');
});

require __DIR__.'/settings.php';
