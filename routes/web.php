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
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CustomDesignController;
use App\Http\Controllers\ChatController;

Route::post('/ai/chat', [ChatController::class, 'chat'])->name('ai.chat');

Route::post('/cart/add/{variantId}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{variantId}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update/{variantId}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/', function () {
    return Inertia::render('HomeView');
})->name('home');

Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::middleware('auth')->group(function () {
    Route::post('/product/{product}/review', [ReviewController::class, 'store'])->name('review.store');
    Route::delete('/product/{product}/review', [ReviewController::class, 'destroy'])->name('review.destroy');
});

Route::get('/MenWear', [ProductPageController::class, 'men'])->name('menwear');
Route::get('/WomanWear', [ProductPageController::class, 'women'])->name('womanwear');

Route::get('/cart', function () {
    $cart  = session()->get('cart', []);
    $views = session()->get('cart_views', []);
    $variantIds = array_keys($cart);

    $customKeys  = array_filter($variantIds, fn($k) => str_starts_with((string)$k, 'custom_'));
    $variantKeys = array_filter($variantIds, fn($k) => !str_starts_with((string)$k, 'custom_'));

    $variants = $variantKeys
        ? ProductVariant::with('product')->whereIn('id', $variantKeys)->get()->keyBy('id')
        : collect();

    $customIds     = array_map(fn($k) => (int) str_replace('custom_', '', $k), $customKeys);
    $customDesigns = $customIds
        ? \App\Models\CustomDesign::whereIn('id', $customIds)->get()->keyBy('id')
        : collect();

    $garmentNames = [
        'tshirt' => 'Krekls', 'hoodie' => 'Hudija', 'jeans' => 'Bikses',
        'tshirt_w' => 'Krekls', 'hoodie_w' => 'Hudija', 'jeans_w' => 'Bikses',
    ];
    $garmentFiles = [
        'tshirt' => 't-shirt_dizaina.png', 'hoodie' => 'Hoodie_dizaina.jpg', 'jeans' => 'Jeans_dizaina.jpg',
        'tshirt_w' => 't-shirt_dizaina.png', 'hoodie_w' => 'Hoodie_dizaina.jpg', 'jeans_w' => 'Jeans_dizaina.jpg',
    ];

    $items = [];
    foreach ($variantIds as $vid) {
        if (str_starts_with((string)$vid, 'custom_')) {
            $designId = (int) str_replace('custom_', '', $vid);
            $design   = $customDesigns->get($designId);
            if (!$design) continue;
            $file = $garmentFiles[$design->garment_id] ?? null;
            $items[] = [
                'variant_id' => $vid,
                'qty'        => (int) ($cart[$vid] ?? 0),
                'name'       => 'Pielāgots ' . ($garmentNames[$design->garment_id] ?? 'dizains'),
                'price'      => 25.00,
                'size'       => '—',
                'color'      => $design->base_color,
                'image'      => $file ? "/designs/garments/{$file}" : null,
                'is_custom'  => true,
            ];
            continue;
        }

        $v = $variants->get((int) $vid);
        if (!$v || !$v->product) continue;
        $p    = $v->product;
        $view = $views[(int)$vid] ?? $views[(string)$vid] ?? 'men';

        $items[] = [
            'variant_id' => (int) $v->id,
            'qty'        => (int) ($cart[$vid] ?? 0),
            'name'       => (string) $p->name,
            'price'      => (float) ($v->price ?? $p->price),
            'size'       => (string) $v->size,
            'color'      => (string) $v->color,
            'image'      => $view === 'women'
                                ? ($p->image_women ?? $p->image_men)
                                : ($p->image_men ?? $p->image_women),
            'is_custom'  => false,
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
    Route::get('/api/orders',              [AdminController::class, 'orders']);
    Route::put('/api/orders/{id}',         [AdminController::class, 'updateOrderStatus']);
    Route::get('/api/users',               [AdminController::class, 'users']);
    Route::put('/api/users/{id}/role',     [AdminController::class, 'updateUserRole']);

});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/validate-coupon', [CheckoutController::class, 'validateCoupon'])->name('checkout.validate-coupon');
    Route::post('/checkout/create-payment-intent', [CheckoutController::class, 'createPaymentIntent'])->name('checkout.payment-intent');

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

    Route::get('/profile/coupons', [CouponController::class, 'index'])->name('profile.coupons');
    Route::post('/profile/coupons/redeem/{coupon}', [CouponController::class, 'redeem'])->name('profile.coupons.redeem');

    Route::get('/profile/designs', [CustomDesignController::class, 'profileIndex'])->name('profile.designs');
    Route::delete('/profile/designs/{id}', [CustomDesignController::class, 'destroy'])->name('profile.designs.destroy');
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
