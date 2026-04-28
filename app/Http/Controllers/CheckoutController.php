<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\PointTransaction;
use App\Models\UserCoupon;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('CheckoutView');
    }

    public function validateCoupon(Request $request)
    {
        $code = strtoupper(trim($request->input('code', '')));

        $userCoupon = UserCoupon::with('coupon')
            ->where('code', $code)
            ->where('user_id', $request->user()->id)
            ->whereNull('used_at')
            ->first();

        if (!$userCoupon) {
            return response()->json(['valid' => false, 'message' => 'Kupons nav atrasts vai jau izmantots.']);
        }

        return response()->json([
            'valid'            => true,
            'discount_percent' => $userCoupon->coupon->discount_percent,
            'name'             => $userCoupon->coupon->name,
        ]);
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
            'coupon_code'    => ['nullable', 'string', 'max:30'],
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

        // Pārbaudīt kuponu
        $userCoupon = null;
        $discountPercent = 0;
        if (!empty($data['coupon_code'])) {
            $userCoupon = UserCoupon::with('coupon')
                ->where('code', strtoupper($data['coupon_code']))
                ->where('user_id', $user->id)
                ->whereNull('used_at')
                ->first();
            if ($userCoupon) {
                $discountPercent = $userCoupon->coupon->discount_percent;
            }
        }

        $order = DB::transaction(function () use ($user, $data, $cart, $variants, $discountPercent, $userCoupon) {
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

            // Piemērot atlaidi
            if ($discountPercent > 0) {
                $total = $total * (1 - $discountPercent / 100);
            }

            $order->update(['total' => round($total, 2)]);

            // Atzīmēt kuponu kā izmantotu
            if ($userCoupon) {
                $userCoupon->update(['used_at' => now()]);
            }

            return $order;
        });

        $request->session()->forget(['cart', 'cart_views']);

        // Punktus piešķir tikai ja netika izmantots kupons
        $earned = 0;
        if (!$userCoupon) {
            $earned = (int) floor($order->total);
            if ($earned > 0) {
                $user->increment('points', $earned);
                PointTransaction::create([
                    'user_id'     => $user->id,
                    'points'      => $earned,
                    'type'        => 'earn',
                    'description' => "Pasūtījums #{$order->id}",
                    'order_id'    => $order->id,
                ]);
            }
        }

        $successMsg = $userCoupon
            ? "Pasūtījums izveidots! Atlaide {$discountPercent}% pielietota. Kvīts nosūtīta uz epastu."
            : "Pasūtījums izveidots! Nopelnīji {$earned} punktus. Kvīts nosūtīta uz epastu.";

        try {
            $order->load('items');
            Mail::to($order->email)->send(new OrderConfirmation($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail sūtīšanas kļūda pasūtījumam #' . $order->id . ': ' . $e->getMessage());
        }

        return redirect()->route('profile.orders.show', ['order' => $order->id])
            ->with('success', $successMsg);
    }
}