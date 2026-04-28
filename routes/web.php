<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::inertia('bed-grid', 'bed-grid')->name('bed-grid');
    Route::inertia('tenants', 'tenants')->name('tenants');
    Route::inertia('finance', 'finance')->name('finance');
});

require __DIR__.'/settings.php';
