<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductVariant;

class CartController extends Controller
{
    public function add(Request $request, $variantId)
    {
        $qty = (int) $request->input('qty', 1);
        if ($qty < 1) $qty = 1;

        ProductVariant::query()
            ->where('is_active', true)
            ->findOrFail($variantId);

        $lastAdd = session()->get('cart_last_add', []);
        if (is_array($lastAdd) && (int) ($lastAdd['variant_id'] ?? 0) === (int) $variantId) {
            $lastAt = (float) ($lastAdd['at'] ?? 0);
            if ($lastAt > 0 && (microtime(true) - $lastAt) < 0.5) {
                return back();
            }
        }

        $cart = session()->get('cart', []);
        $current = $cart[$variantId] ?? 0;
        if (is_array($current)) {
            $current = (int)($current['qty'] ?? 0);
        }
        $cart[$variantId] = (int)$current + $qty;
        session()->put('cart', $cart);

        // Saglabājam view (men/women) katram variantam
        $views = session()->get('cart_views', []);
        $views[$variantId] = $request->input('view', 'men');
        session()->put('cart_views', $views);

        session()->put('cart_last_add', [
            'variant_id' => (int) $variantId,
            'at' => microtime(true),
        ]);

        return back()->with('success', 'Produkts pievienots grozam!');
    }

    public function remove($variantId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$variantId]);
        session()->put('cart', $cart);

        $views = session()->get('cart_views', []);
        unset($views[$variantId]);
        session()->put('cart_views', $views);

        return back();
    }

    // DEBUG - DZĒST PĒC PĀRBAUDES
    public function debugCart()
    {
        dd([
            'cart' => session()->get('cart', []),
            'cart_views' => session()->get('cart_views', []),
        ]);
    }

    public function update(Request $request, $variantId)
    {
        $qty = (int) $request->input('qty', 1);
        $cart = session()->get('cart', []);

        if ($qty <= 0) {
            unset($cart[$variantId]);
            $views = session()->get('cart_views', []);
            unset($views[$variantId]);
            session()->put('cart_views', $views);
        } else {
            $cart[$variantId] = $qty;
        }

        session()->put('cart', $cart);
        return back();
    }

    public function clear()
    {
        session()->forget('cart');
        session()->forget('cart_views');
        return back();
    }
}