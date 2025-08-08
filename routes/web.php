<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SetController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\EventsController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

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


    // Places - only admin can access index
    Route::get('places', [PlaceController::class, 'index'])->name('places.index')->middleware('role:admin');
    Route::get('places/create', [PlaceController::class, 'create'])->name('places.create')->middleware('role:admin,placeManager');
    Route::post('places', [PlaceController::class, 'store'])->name('places.store')->middleware('role:admin,placeManager');
    Route::get('places/{place}/edit', [PlaceController::class, 'edit'])->name('places.edit')->middleware('role:admin,placeManager');
    Route::put('places/{place}', [PlaceController::class, 'update'])->name('places.update')->middleware('role:admin,placeManager');
    Route::patch('places/{place}', [PlaceController::class, 'update'])->name('places.update')->middleware('role:admin,placeManager');
    Route::delete('places/{place}', [PlaceController::class, 'destroy'])->name('places.destroy')->middleware('role:admin,placeManager');

    // Events - admin, dj, placeManager can access
    Route::resource('events', EventsController::class)->middleware('role:admin,dj,placeManager');
    
    // DJ Applications
    Route::get('dj/application/create', [App\Http\Controllers\DjApplicationController::class, 'create'])->name('dj.application.create')->middleware('auth');
    Route::post('dj/application', [App\Http\Controllers\DjApplicationController::class, 'store'])->name('dj.application.store')->middleware('auth');
    Route::get('dj/application/status', [App\Http\Controllers\DjApplicationController::class, 'status'])->name('dj.application.status')->middleware('auth');
    
    // Admin DJ Applications
    Route::get('admin/dj-applications', [App\Http\Controllers\DjApplicationController::class, 'index'])->name('admin.dj-applications.index')->middleware('role:admin');
    Route::get('admin/dj-applications/{djApplication}', [App\Http\Controllers\DjApplicationController::class, 'show'])->name('admin.dj-applications.show')->middleware('role:admin');
    Route::post('admin/dj-applications/{djApplication}/approve', [App\Http\Controllers\DjApplicationController::class, 'approve'])->name('admin.dj-applications.approve')->middleware('role:admin');
    Route::post('admin/dj-applications/{djApplication}/reject', [App\Http\Controllers\DjApplicationController::class, 'reject'])->name('admin.dj-applications.reject')->middleware('role:admin');
    
    // PlaceManager place creation - for new placeManagers
    Route::get('placemanager/place/create', [PlaceController::class, 'createForPlaceManager'])->name('placemanager.place.create')->middleware('auth');
    Route::post('placemanager/place', [PlaceController::class, 'storeForPlaceManager'])->name('placemanager.place.store')->middleware('auth');
    
    // PlaceManager direct edit - redirect to their place edit page
    Route::get('placemanager/place/edit', [PlaceController::class, 'editMyPlace'])->name('placemanager.place.edit')->middleware('role:placeManager');
    
    // Premium management - only placeManagers can access
    Route::get('premium', [App\Http\Controllers\PremiumController::class, 'index'])->name('premium.index')->middleware('role:placeManager');
    Route::post('premium/trial', [App\Http\Controllers\PremiumController::class, 'startFreeTrial'])->name('premium.trial')->middleware('role:placeManager');
    Route::post('premium/purchase', [App\Http\Controllers\PremiumController::class, 'purchase'])->name('premium.purchase')->middleware('role:placeManager');
    
    // DJ Offers - PlaceManagers and DJs can access
    Route::get('dj-offers/dj-list', [App\Http\Controllers\DjOfferController::class, 'djList'])->name('dj-offers.dj-list')->middleware('role:placeManager');
    Route::get('dj-offers/create/{dj}', [App\Http\Controllers\DjOfferController::class, 'create'])->name('dj-offers.create')->middleware('role:placeManager');
    Route::post('dj-offers', [App\Http\Controllers\DjOfferController::class, 'store'])->name('dj-offers.store')->middleware('role:placeManager');
    Route::get('dj-offers', [App\Http\Controllers\DjOfferController::class, 'index'])->name('dj-offers.index')->middleware('role:placeManager,dj');
    Route::get('dj-offers/{offer}', [App\Http\Controllers\DjOfferController::class, 'show'])->name('dj-offers.show')->middleware('role:placeManager,dj');
    Route::post('dj-offers/{offer}/accept', [App\Http\Controllers\DjOfferController::class, 'accept'])->name('dj-offers.accept')->middleware('role:dj');
    Route::post('dj-offers/{offer}/reject', [App\Http\Controllers\DjOfferController::class, 'reject'])->name('dj-offers.reject')->middleware('role:dj');
    Route::post('dj-offers/{offer}/cancel', [App\Http\Controllers\DjOfferController::class, 'cancel'])->name('dj-offers.cancel')->middleware('role:placeManager');
    
    // Offer Messages - API endpoints for messaging
    Route::get('api/offers/{offer}/messages', [App\Http\Controllers\OfferMessageController::class, 'index'])->name('offer-messages.index')->middleware('role:placeManager,dj');
    Route::post('api/offers/{offer}/messages', [App\Http\Controllers\OfferMessageController::class, 'store'])->name('offer-messages.store')->middleware('role:placeManager,dj');

    // Profile routes - all authenticated users can access
    Route::get('profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::match(['patch', 'post'], 'profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('profile/update-password', [UserController::class, 'updatePassword'])->name('profile.update-password');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/settings.php';
