<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductPageController extends Controller
{
    private function filteredProducts(Request $request, string $gender)
    {
        $search   = (string) $request->query('search', '');
        $category = (string) $request->query('category', 'all');
        $minPrice = (float)  $request->query('minPrice', 0);
        $maxPrice = (float)  $request->query('maxPrice', 100000);

        $q = Product::query()
            ->whereIn('gender', [$gender, 'unisex'])
            ->when($category !== 'all', fn($qq) => $qq->where('category', $category))
            ->when($search !== '', fn($qq) => $qq->where('name', 'like', "%{$search}%"))
            ->whereBetween('price', [$minPrice, $maxPrice])
            ->orderBy('id');

        return $q->paginate(12)->withQueryString();
    }

    public function men(Request $request)
    {
         return Inertia::render('MenView', [
        'products' => $this->filteredProducts($request, 'men'),
        'filters'  => [
            'search'   => (string) $request->query('search', ''),
            'category' => (string) $request->query('category', 'all'),
            'minPrice' => (float)  $request->query('minPrice', 0),
            'maxPrice' => (float)  $request->query('maxPrice', 1000),
        ],
    ]);
    }

    public function women(Request $request)
    {
        return Inertia::render('WomanView', [
        'products' => $this->filteredProducts($request, 'women'),
        'filters'  => [
            'search'   => (string) $request->query('search', ''),
            'category' => (string) $request->query('category', 'all'),
            'minPrice' => (float)  $request->query('minPrice', 0),
            'maxPrice' => (float)  $request->query('maxPrice', 1000),
        ],
    ]);
    }
}
