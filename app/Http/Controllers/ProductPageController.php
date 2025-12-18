<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class ProductPageController extends Controller
{
    public function men()
    {
        return Inertia::render('MenView', [
            'products' => Product::orderBy('id')->get(),
        ]);
    }

    public function women()
    {
        return Inertia::render('WomanView', [
            'products' => Product::orderBy('id')->get(),
        ]);
    }
}
