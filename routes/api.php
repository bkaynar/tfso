<?php

use App\Http\Controllers\API\AccessLogController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\FavoriteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\TrackController;
use App\Http\Controllers\API\DJController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Favorites
    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::get('/', [FavoriteController::class, 'index'])->name('index');
        Route::get('/tracks', [FavoriteController::class, 'tracks'])->name('tracks');
        Route::get('/sets', [FavoriteController::class, 'sets'])->name('sets');
        Route::get('/radios', [FavoriteController::class, 'radios'])->name('radios');
        Route::get('/djs', [FavoriteController::class, 'djs'])->name('djs');
    });

    Route::post('/tracks/{track}/toggle-favorite', [FavoriteController::class, 'toggleTrack'])->name('tracks.toggle-favorite');
    Route::post('/sets/{set}/toggle-favorite', [FavoriteController::class, 'toggleSet'])->name('sets.toggle-favorite');
    Route::post('/radios/{radio}/toggle-favorite', [FavoriteController::class, 'toggleRadio'])->name('radios.toggle-favorite');
    Route::post('/djs/{dj}/toggle-favorite', [FavoriteController::class, 'toggleDj'])->name('djs.toggle-favorite');

    // DJ Routes (Authenticated)
    Route::post('/djs', [DJController::class, 'store']);
    Route::post('/djs/{id}', [DJController::class, 'update']); // Use POST for multipart/form-data updates
    Route::delete('/djs/{id}', [DJController::class, 'destroy']);
});


Route::post('/categories/bulk', [CategoryController::class, 'storeBulk']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}/tracks', [CategoryController::class, 'getTracksByCategory']);
Route::get('/categories/{id}/sets', [CategoryController::class, 'getSetsByCategory']);

// Public DJ Routes
Route::get('/djs', [DJController::class, 'index']);
Route::get('/djs/{id}', [DJController::class, 'show']);

Route::apiResource('tracks', TrackController::class);
Route::apiResource('access-logs', AccessLogController::class)->only([
    'index',
    'store',
    'show'
]);
