<?php

use App\Http\Controllers\EventController;
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
    // Categories - DJ can only view (index), Admin can do everything
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index')->middleware('role:admin,dj');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create')->middleware('role:admin');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store')->middleware('role:admin');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit')->middleware('role:admin');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware('role:admin');
    Route::patch('categories/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware('role:admin');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('role:admin');

    // Sets - admin, dj can access
    Route::resource('sets', SetController::class)->middleware('role:admin,dj');

    // Tracks - admin, dj can access
    Route::resource('tracks', TrackController::class)->middleware('role:admin,dj');

    // Users - only admin can access
    Route::resource('users', UserController::class)->middleware('role:admin');

    //Events - admin, dj can access
    Route::resource('events', EventController::class)->middleware('role:admin,dj');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
