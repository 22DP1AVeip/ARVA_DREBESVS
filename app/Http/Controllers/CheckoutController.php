<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Atver checkout lapu (UI lasa cart no Inertia shared props)
        return Inertia::render('CheckoutView');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:190'],
            'city' => ['required', 'string', 'max:120'],
            'postcode' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:120'],
            'payment_method' => ['required', 'in:card,cod'],
        ]);

        $cart = $request->session()->get('cart', []); // [productId => qty]

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Grozs ir tukšs.');
        }

        $productIds = array_keys($cart);

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        foreach ($productIds as $pid) {
            if (!isset($products[$pid])) {
                return redirect('/cart')->with('error', 'Kāda prece vairs nav pieejama. Lūdzu, pārbaudi grozu.');
            }
        }

        $user = $request->user();

        $order = DB::transaction(function () use ($user, $data, $cart, $products) {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'processing',
                'total' => 0,

                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'],
                'city' => $data['city'],
                'postcode' => $data['postcode'],
                'country' => $data['country'],
            ]);

            $total = 0;

            foreach ($cart as $productId => $qty) {
                $p = $products[$productId];

                $name = $p->name;
                $price = (float) $p->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $p->id,
                    'name' => $name,
                    'price' => $price,
                    'qty' => (int) $qty,
                ]);

                $total += $price * (int) $qty;
            }

            $order->update(['total' => round($total, 2)]);

            return $order;
        });

        $request->session()->forget('cart');

        return redirect()->route('profile.orders.show', ['order' => $order->id])
            ->with('success', 'Pasūtījums izveidots!');
    }
}
