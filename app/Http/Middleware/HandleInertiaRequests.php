<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\CustomDesign;
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

                // Sadalīt custom dizainu un parasto variantu atslēgas
                $customKeys  = array_filter($variantIds, fn($k) => str_starts_with((string)$k, 'custom_'));
                $variantKeys = array_filter($variantIds, fn($k) => !str_starts_with((string)$k, 'custom_'));

                $variants = $variantKeys
                    ? ProductVariant::with('product')->whereIn('id', $variantKeys)->get()->keyBy('id')
                    : collect();

                // Custom dizainu modeļi
                $customIds     = array_map(fn($k) => (int) str_replace('custom_', '', $k), $customKeys);
                $customDesigns = $customIds
                    ? CustomDesign::whereIn('id', $customIds)->get()->keyBy('id')
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
                foreach ($variantIds as $variantId) {
                    if (str_starts_with((string)$variantId, 'custom_')) {
                        $designId = (int) str_replace('custom_', '', $variantId);
                        $design   = $customDesigns->get($designId);
                        if (!$design) continue;
                        $file = $garmentFiles[$design->garment_id] ?? null;
                        $items[] = [
                            'id'        => $variantId,
                            'name'      => 'Pielāgots ' . ($garmentNames[$design->garment_id] ?? 'dizains'),
                            'price'     => 25.00,
                            'image'     => $file ? "/designs/garments/{$file}" : null,
                            'is_custom' => true,
                            'color'     => $design->base_color,
                            'size'      => '—',
                            'qty'       => (int) ($cart[$variantId] ?? 0),
                        ];
                        continue;
                    }

                    $variant = $variants->get((int) $variantId);
                    if (!$variant || !$variant->product) continue;

                    $product = $variant->product;
                    $view    = $views[$variantId] ?? $views[(int)$variantId] ?? 'men';

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
                        'is_custom'   => false,
                    ];
                }

                return [
                    'count' => $count,
                    'items' => $items,
                ];
            },

            // FLASH
            'flash' => [
                'success' => session()->pull('success'),
                'error'   => session()->pull('error'),
            ],

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