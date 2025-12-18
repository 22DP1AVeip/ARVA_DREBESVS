<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

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

Route::get('/cart', function () {
    return Inertia::render('CartView');
})->name('cart.view');

Route::get('/checkout', function () {
    return Inertia::render('CheckoutView');
})->name('checkout');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
