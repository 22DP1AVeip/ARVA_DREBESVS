<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\Product;


class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            
            
            'cart' => function () {
                $cart = session()->get('cart', []); // [id => qty]
                $count = array_sum($cart);

                $ids = array_keys($cart);
                $products = $ids
                ? Product::query()->whereIn('id', $ids)->get()->keyBy('id')
                : collect();

                $items = [];
                foreach ($cart as $id => $qty) {
                     $p = $products->get((int)$id);
                         if (!$p) continue;

                $items[] = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => (float) $p->price,
                    'image_men' => $p->image_men,
                    'image_women' => $p->image_women,
                    'qty' => (int) $qty,
                ];
}

                return [
                    'count' => $count,
                    'items' => $items,
                ];
            },
        ];
    }
}

