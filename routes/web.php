<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductPageController;

// Home
Route::get('/', function () {
    return Inertia::render('HomeView');
})->name('home');

// Product pages (ONLY controller versions)
Route::get('/MenWear', [ProductPageController::class, 'men'])->name('menwear');
Route::get('/WomanWear', [ProductPageController::class, 'women'])->name('womanwear');

// Favorites
Route::get('/favorites', function () {
    return Inertia::render('FavoritesView');
})->name('favorites');

// Admin
Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

// Other routes
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
