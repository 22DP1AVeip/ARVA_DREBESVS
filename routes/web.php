<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FavoriteController;
use App\Models\ProductVariant;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomDesignController;

Route::post('/cart/add/{variantId}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{variantId}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update/{variantId}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/', function () {
    return Inertia::render('HomeView');
})->name('home');

Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

Route::get('/MenWear', [ProductPageController::class, 'men'])->name('menwear');
Route::get('/WomanWear', [ProductPageController::class, 'women'])->name('womanwear');

Route::get('/cart', function () {
    $cart  = session()->get('cart', []);
    $views = session()->get('cart_views', []);
    $variantIds = array_keys($cart);

    $variants = ProductVariant::with('product')
        ->whereIn('id', $variantIds)
        ->get()
        ->keyBy('id');

    $items = [];
    foreach ($variantIds as $vid) {
        if (!isset($variants[$vid])) continue;

        $v = $variants[$vid];
        $p = $v->product;
        $view = $views[(int)$vid] ?? $views[(string)$vid] ?? 'men';

        $items[] = [
            'variant_id'  => (int) $v->id,
            'qty'         => (int) ($cart[$vid] ?? 0),
            'name'        => (string) $p->name,
            'price'       => (float) ($v->price ?? $p->price),
            'size'        => (string) $v->size,
            'color'       => (string) $v->color,
            'image'       => $view === 'women'
                                ? ($p->image_women ?? $p->image_men)
                                : ($p->image_men ?? $p->image_women),
        ];
    }
    return Inertia::render('CartView', [
        'items' => $items,
    ]);
})->name('cart.view');

Route::get('/favorites', function () {
    return Inertia::render('FavoritesView');
})->name('favorites');

Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {

    // Dashboard (blade view)
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // API endpoints (JSON)
    Route::get('/api/stats',           [AdminController::class, 'stats']);
    Route::get('/api/products',        [AdminController::class, 'products']);
    Route::get('/api/products/{id}',   [AdminController::class, 'product']);
    Route::post('/api/products',       [AdminController::class, 'storeProduct']);
    Route::put('/api/products/{id}',   [AdminController::class, 'updateProduct']);
    Route::delete('/api/products/{id}',[AdminController::class, 'destroyProduct']);
    Route::get('/api/orders',          [AdminController::class, 'orders']);
    Route::get('/api/users',           [AdminController::class, 'users']);

});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/dashboard', function () {
        return Inertia::render('HomeView');
    })->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile/{section?}', [ProfileController::class, 'show'])
        ->where('section', 'settings')
        ->name('profile');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    Route::get('/profile/orders', [OrderController::class, 'index'])->name('profile.orders');
    Route::get('/profile/orders/{order}', [OrderController::class, 'show'])->name('profile.orders.show');

    Route::get('/profile/favorites', [FavoriteController::class, 'index'])->name('profile.favorites');
    Route::post('/favorites/toggle/{productId}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
});

Route::get('/debug-cart', function() {
    dd([
        'cart' => session()->get('cart', []),
        'cart_views' => session()->get('cart_views', []),
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/design', [CustomDesignController::class, 'create'])->name('design.index');
    Route::post('/design/store', [CustomDesignController::class, 'store'])->name('design.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/design/{id}', [CustomDesignController::class, 'show'])->name('design.show');
    Route::post('/design/{id}/cart', [CustomDesignController::class, 'addToCart'])->name('design.cart');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
