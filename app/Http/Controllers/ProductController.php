<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function show(Request $request, $id)
    {
        $product = Product::query()->findOrFail($id);
        $view = $request->query('view', 'men');

        return Inertia::render('ProductShow', [
            'product' => $product,
            'view' => $view,
        ]);
    }
}
