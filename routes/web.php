<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Product;

// Root route named 'home'
Route::get('/', function () {
    return Inertia::render('HomeView'); // Points to 'HomeView.vue' in resources/js/pages/
})->name('home');

Route::get('/MenWear', function () {
    return Inertia::render('MenView', [
        'products' => Product::orderBy('id')->get(),
    ]);
});

Route::get('/WomanWear', function () {
    return Inertia::render('WomanView', [
        'products' => Product::orderBy('id')->get(),
    ]);
});

Route::get('/favorites', function () {
    return Inertia::render('FavoritesView'); // The Vue page component name
})->name('favorites');

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard'); // create this view
    })->name('admin.dashboard');
});

// Include other routes like auth and settings
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
