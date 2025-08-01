<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\FavoriteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\TrackController;
use App\Http\Controllers\API\DJController;
use App\Http\Controllers\API\SetController;
use App\Http\Controllers\API\AccessLogController;
use App\Http\Controllers\API\StageFeedController;

Route::post('/mobile-register', [AuthController::class, 'register']);
Route::post('/mobile-login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken']);

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

    // Access Logs (Authenticated)
    Route::apiResource('access-logs', AccessLogController::class)->only([
        'index',
        'store'
    ]);
    // Global Access Logs (Authenticated)
    Route::get('/access-logs/global', [AccessLogController::class, 'globalIndex']);
});


Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories-paginated', [CategoryController::class, 'paginatedIndex']);
Route::get('/categories/search', [CategoryController::class, 'search']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/{id}/tracks', [CategoryController::class, 'getTracksByCategory']);
Route::get('/categories/{id}/sets', [CategoryController::class, 'getSetsByCategory']);

// Public DJ Routes
Route::get('/djs', [DJController::class, 'index']);
Route::get('/djs-paginated', [DJController::class, 'paginatedIndex']);
Route::get('/djs/search', [DJController::class, 'search']);
Route::get('/djs/{id}', [DJController::class, 'show']);

// 🔥 Yeni Çıkanlar Endpoint'i (Public)
Route::get('/tracks/new-releases', [TrackController::class, 'newReleases']);
Route::get('sets-latest', [SetController::class, 'latest']);

//Tüm Setleri Pageable Listeler
Route::get('/sets', [SetController::class, 'index']);
Route::get('/sets/search', [SetController::class, 'search']);

Route::get('/tracks/search', [TrackController::class, 'search']);
Route::apiResource('tracks', TrackController::class);

// Stage Feed
Route::get('/stage-feed', [StageFeedController::class, 'index']);
