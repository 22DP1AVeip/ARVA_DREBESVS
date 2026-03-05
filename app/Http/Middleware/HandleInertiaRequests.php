<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Favorite;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return array_merge(parent::share($request), [

            'name' => config('app.name'),

            'quote' => [
                'message' => trim($message),
                'author' => trim($author),
            ],

            'auth' => [
                'user' => $request->user(),
            ],

            'ziggy' => function () use ($request) {
                return [
                    ...(new Ziggy)->toArray(),
                    'location' => $request->url(),
                ];
            },

            'sidebarOpen' => ! $request->hasCookie('sidebar_state')
                || $request->cookie('sidebar_state') === 'true',

            // CART
            'cart' => function () {
                $cart = session()->get('cart', []);
                $views = session()->get('cart_views', []);
                $count = array_sum($cart);

                $variantIds = array_keys($cart);
                $variants = $variantIds
                    ? ProductVariant::with('product')
                        ->whereIn('id', $variantIds)
                        ->get()
                        ->keyBy('id')
                    : collect();

                $items = [];
                foreach ($variantIds as $variantId) {
                    $variant = $variants->get((int) $variantId);
                    if (!$variant || !$variant->product) {
                        continue;
                    }

                    $product = $variant->product;
                    $view = $views[$variantId] ?? $views[(int)$variantId] ?? 'men';

                    $items[] = [
                        'id'          => (int) $variant->id,
                        'name'        => (string) $product->name,
                        'price'       => (float) ($variant->price ?? $product->price),
                        'image_men'   => $product->image_men,
                        'image_women' => $product->image_women,
                        'image'       => $view === 'women'
                                            ? ($product->image_women ?? $product->image_men)
                                            : ($product->image_men ?? $product->image_women),
                        'qty'         => (int) ($cart[$variantId] ?? 0),
                    ];
                }

                return [
                    'count' => $count,
                    'items' => $items,
                ];
            },

            // FAVORITES
            'favoritesIds' => function () use ($request) {
                $user = $request->user();
                if (!$user) return [];

                return Favorite::query()
                    ->where('user_id', $user->id)
                    ->pluck('product_id')
                    ->toArray();
            },

        ]);
    }
}