<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\CustomDesign;
use App\Models\PointTransaction;
use App\Models\UserCoupon;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    private const CUSTOM_DESIGN_PRICE = 25.00;

    private const CUSTOM_GARMENT_NAMES = [
        'tshirt' => 'Krekls', 'hoodie' => 'Hudija', 'jeans' => 'Bikses',
        'tshirt_w' => 'Krekls', 'hoodie_w' => 'Hudija', 'jeans_w' => 'Bikses',
    ];

    public function index(Request $request)
    {
        $user      = $request->user();
        $lastOrder = $user->orders()->latest()->first();

        return Inertia::render('CheckoutView', [
            'prefill' => [
                'full_name' => $lastOrder?->full_name ?? $user->name,
                'email'     => $lastOrder?->email     ?? $user->email,
                'phone'     => $lastOrder?->phone     ?? '',
                'address'   => $lastOrder?->address   ?? '',
                'city'      => $lastOrder?->city      ?? '',
                'postcode'  => $lastOrder?->postcode  ?? '',
                'country'   => $lastOrder?->country   ?? 'Latvija',
            ],
        ]);
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

    public function createPaymentIntent(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Grozs ir tukšs.'], 400);
        }

        $variantIds = array_filter(array_keys($cart), fn($k) => !str_starts_with((string) $k, 'custom_'));
        $variants = ProductVariant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $total = 0;
        foreach ($cart as $key => $qty) {
            if (str_starts_with((string) $key, 'custom_')) {
                $total += self::CUSTOM_DESIGN_PRICE * (int) $qty;
                continue;
            }

            $variant = $variants->get((int) $key);
            if (!$variant) continue;
            $price  = (float) ($variant->price ?? $variant->product->price);
            $total += $price * (int) $qty;
        }

        // Piemērot atlaidi ja kupons norādīts
        $couponCode = strtoupper(trim($request->input('coupon_code', '')));
        if ($couponCode) {
            $userCoupon = UserCoupon::with('coupon')
                ->where('code', $couponCode)
                ->where('user_id', $request->user()->id)
                ->whereNull('used_at')
                ->first();
            if ($userCoupon) {
                $total = $total * (1 - $userCoupon->coupon->discount_percent / 100);
            }
        }

        $amountCents = (int) round($total * 100);

        if ($amountCents < 50) {
            return response()->json(['error' => 'Summa ir pārāk maza.'], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount'                    => $amountCents,
            'currency'                  => 'eur',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return response()->json(['client_secret' => $paymentIntent->client_secret]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'         => ['required', 'string', 'max:120'],
            'email'             => ['required', 'email', 'max:190'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'address'           => ['required', 'string', 'max:190'],
            'city'              => ['required', 'string', 'max:120'],
            'postcode'          => ['required', 'string', 'max:30'],
            'country'           => ['required', 'string', 'max:120'],
            'payment_method'    => ['required', 'in:card,cod'],
            'coupon_code'       => ['nullable', 'string', 'max:30'],
            'payment_intent_id' => ['nullable', 'string', 'max:100'],
        ]);

        // Verificēt Stripe maksājumu kartes gadījumā
        if ($data['payment_method'] === 'card') {
            if (empty($data['payment_intent_id'])) {
                return back()->withErrors(['payment' => 'Maksājums nav veikts.']);
            }

            Stripe::setApiKey(config('services.stripe.secret'));
            $pi = PaymentIntent::retrieve($data['payment_intent_id']);

            if ($pi->status !== 'succeeded') {
                return back()->withErrors(['payment' => 'Maksājums nav apstiprināts.']);
            }
        }

        $cart  = $request->session()->get('cart', []);
        $views = $request->session()->get('cart_views', []);

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Grozs ir tukšs.');
        }

        $variantIds = array_filter(array_keys($cart), fn($k) => !str_starts_with((string) $k, 'custom_'));

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

        $customKeys = array_filter(array_keys($cart), fn($k) => str_starts_with((string) $k, 'custom_'));
        $customDesignIds = array_map(fn($k) => (int) str_replace('custom_', '', $k), $customKeys);
        $customDesigns = $customDesignIds
            ? CustomDesign::whereIn('id', $customDesignIds)->where('user_id', $user->id)->get()->keyBy('id')
            : collect();

        foreach ($customKeys as $key) {
            $designId = (int) str_replace('custom_', '', $key);
            if (!$customDesigns->has($designId)) {
                return redirect('/cart')->with('error', 'Kāda prece vairs nav pieejama.');
            }
        }

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

        $order = DB::transaction(function () use ($user, $data, $cart, $variants, $customDesigns, $discountPercent, $userCoupon) {
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

            foreach ($cart as $key => $qty) {
                if (str_starts_with((string) $key, 'custom_')) {
                    $design = $customDesigns->get((int) str_replace('custom_', '', $key));
                    $price  = self::CUSTOM_DESIGN_PRICE;

                    OrderItem::create([
                        'order_id'           => $order->id,
                        'product_id'         => null,
                        'product_variant_id' => null,
                        'name'               => 'Pielāgots ' . (self::CUSTOM_GARMENT_NAMES[$design->garment_id] ?? 'dizains'),
                        'price'              => $price,
                        'qty'                => (int) $qty,
                        'color'              => $design->base_color,
                        'size'               => '—',
                    ]);

                    $total += $price * (int) $qty;
                    continue;
                }

                $variant = $variants->get((int) $key);
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

            if ($discountPercent > 0) {
                $total = $total * (1 - $discountPercent / 100);
            }

            $order->update(['total' => round($total, 2)]);

            if ($userCoupon) {
                $userCoupon->update(['used_at' => now()]);
            }

            return $order;
        });

        $request->session()->forget(['cart', 'cart_views']);

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
