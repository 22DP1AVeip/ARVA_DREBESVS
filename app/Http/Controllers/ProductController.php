<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function show(Request $request, $id)
    {
        $view = $request->query('view', 'men');

        $product = Product::with(['variants' => function ($q) {
            $q->where('is_active', true)
              ->orderBy('color')
              ->orderBy('size');
        }])->findOrFail($id);

        return Inertia::render('ProductShow', [
            'product' => $product,
            'view' => $view,
            'variants' => $product->variants->map(fn ($v) => [
                'id' => $v->id,
                'size' => $v->size,
                'color' => $v->color,
                'price' => $v->price, // nullable
            ])->values(),
        ]);
    }
}
