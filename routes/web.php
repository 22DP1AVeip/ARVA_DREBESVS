<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\ProductController;


Route::get('/', function () {
    return Inertia::render('HomeView');
})->name('home');

Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

Route::get('/MenWear', [ProductPageController::class, 'men'])->name('menwear');
Route::get('/WomanWear', [ProductPageController::class, 'women'])->name('womanwear');


Route::get('/favorites', function () {
    return Inertia::render('FavoritesView');
})->name('favorites');


Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
