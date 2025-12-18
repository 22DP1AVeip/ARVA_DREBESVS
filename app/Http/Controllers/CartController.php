<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function add(Request $request, $id)
    {
        $qty = (int) $request->input('qty', 1);
        if ($qty < 1) $qty = 1;

        // pārbaudām, ka produkts eksistē
        Product::query()->findOrFail($id);

        $cart = session()->get('cart', []); // [productId => qty]
        $cart[$id] = ($cart[$id] ?? 0) + $qty;

        session()->put('cart', $cart);

        return back()->with('success', 'Produkts pievienots grozam!');
    }

    public function remove(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session()->put('cart', $cart);

        return back();
    }

    public function update(Request $request, $id)
    {
        $qty = (int) $request->input('qty', 1);

        $cart = session()->get('cart', []);
        if ($qty <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $qty;
        }
        session()->put('cart', $cart);

        return back();
    }

    public function clear()
    {
        session()->forget('cart');
        return back();
    }
}
