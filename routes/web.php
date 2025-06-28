<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SetController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Categories - only admin and dj can access
    Route::resource('categories', CategoryController::class)->except(['show'])->middleware('role:admin,dj');

    // Sets - admin, dj can access
    Route::resource('sets', SetController::class)->middleware('role:admin,dj');

    // Tracks - admin, dj can access
    Route::resource('tracks', TrackController::class)->middleware('role:admin,dj');

    // Users - only admin can access
    Route::resource('users', UserController::class)->middleware('role:admin');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
