<?php

use App\Http\Controllers\API\AccessLogController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\FavoriteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\TrackController;
use App\Http\Controllers\API\DJController;
use App\Http\Controllers\API\SetController;

Route::post('/mobile-register', [AuthController::class, 'register']);
Route::post('/mobile-login', [AuthController::class, 'login']);

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

});
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories/bulk', [CategoryController::class, 'storeBulk']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/{id}/tracks', [CategoryController::class, 'getTracksByCategory']);
Route::get('/categories/{id}/sets', [CategoryController::class, 'getSetsByCategory']);

// 🔥 Yeni Çıkanlar Endpoint'i (Public)
Route::get('/tracks/new-releases', [TrackController::class, 'newReleases']);
Route::get('sets-latest', [SetController::class, 'latest']);

Route::get('/tracks', [TrackController::class, 'index']);
Route::post('/tracks', [TrackController::class, 'store'])->middleware('auth:sanctum');
Route::get('/tracks/{id}', [TrackController::class, 'show']);
Route::post('/tracks/{id}', [TrackController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/tracks/{id}', [TrackController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('/sets', [SetController::class, 'index']);

Route::get('/djs', [DJController::class, 'index']);
Route::post('/djs', [DJController::class, 'store'])->middleware('auth:sanctum');
Route::get('/djs/{id}', [DJController::class, 'show']);
Route::post('/djs/{id}', [DJController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/djs/{id}', [DJController::class, 'destroy'])->middleware('auth:sanctum');





Route::apiResource('access-logs', AccessLogController::class)->only([
    'index',
    'store',
    'show'
]);
