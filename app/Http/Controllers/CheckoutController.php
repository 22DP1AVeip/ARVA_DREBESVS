<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('CheckoutView');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'      => ['required', 'string', 'max:120'],
            'email'          => ['required', 'email', 'max:190'],
            'phone'          => ['nullable', 'string', 'max:50'],
            'address'        => ['required', 'string', 'max:190'],
            'city'           => ['required', 'string', 'max:120'],
            'postcode'       => ['required', 'string', 'max:30'],
            'country'        => ['required', 'string', 'max:120'],
            'payment_method' => ['required', 'in:card,cod'],
        ]);

        $cart  = $request->session()->get('cart', []);   // [variantId => qty]
        $views = $request->session()->get('cart_views', []);

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Grozs ir tukšs.');
        }

        $variantIds = array_keys($cart);

        $variants = ProductVariant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        foreach ($variantIds as $vid) {
            if (!isset($variants[$vid])) {
                return redirect('/cart')->with('error', 'Kāda prece vairs nav pieejama.');
            }
        }

        $user = $request->user();

        $order = DB::transaction(function () use ($user, $data, $cart, $variants) {
            $order = Order::create([
                'user_id'    => $user->id,
                'status'     => 'processing',
                'total'      => 0,
                'full_name'  => $data['full_name'],
                'email'      => $data['email'],
                'phone'      => $data['phone'] ?? null,
                'address'    => $data['address'],
                'city'       => $data['city'],
                'postcode'   => $data['postcode'],
                'country'    => $data['country'],
            ]);

            $total = 0;

            foreach ($cart as $variantId => $qty) {
                $variant = $variants->get((int) $variantId);
                $product = $variant->product;
                $price   = (float) ($variant->price ?? $product->price);

                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $product->id,
                    'product_variant_id' => $variant->id,
                    'name'               => $product->name,
                    'price'              => $price,
                    'qty'                => (int) $qty,
                    'color'              => $variant->color,
                    'size'               => $variant->size,
                ]);

                $total += $price * (int) $qty;
            }

            $order->update(['total' => round($total, 2)]);

            return $order;
        });

        $request->session()->forget(['cart', 'cart_views']);

        try {
            $order->load('items');
            Mail::to($data['email'])->send(new OrderConfirmation($order));
        } catch (\Exception $e) {
        }

        return redirect()->route('profile.orders.show', ['order' => $order->id])
            ->with('success', 'Pasūtījums izveidots! Kvīts nosūtīta uz epastu.');
    }
}