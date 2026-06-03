<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // ─── STATS ───────────────────────────────────────────
    public function stats()
    {
        return response()->json([
            'products' => Product::count(),
            'variants' => ProductVariant::count(),
            'orders'   => class_exists(Order::class) ? Order::count() : 0,
            'users'    => User::count(),
        ]);
    }

    // ─── PRODUCTS ────────────────────────────────────────
    public function products()
    {
        $products = Product::withCount('variants')->orderByDesc('id')->get();
        return response()->json($products);
    }

    public function product($id)
    {
        $product = Product::with(['variants' => fn($q) => $q->orderBy('color')->orderBy('size')])
            ->findOrFail($id);
        return response()->json($product);
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'price'    => 'required|numeric|min:0',
            'category' => 'required|string',
            'gender'   => 'required|in:men,women,unisex',
        ]);

        $product = Product::create([
            'name'         => $request->name,
            'price'        => $request->price,
            'category'     => $request->category,
            'gender'       => $request->gender,
            'image_men'    => $request->image_men,
            'image_women'  => $request->image_women,
        ]);

        $this->syncVariants($product, $request->variants ?? []);

        return response()->json($product->load('variants'), 201);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'price'    => 'required|numeric|min:0',
            'category' => 'required|string',
            'gender'   => 'required|in:men,women,unisex',
        ]);

        $product->update([
            'name'        => $request->name,
            'price'       => $request->price,
            'category'    => $request->category,
            'gender'      => $request->gender,
            'image_men'   => $request->image_men,
            'image_women' => $request->image_women,
        ]);

        $this->syncVariants($product, $request->variants ?? []);

        return response()->json($product->load('variants'));
    }

    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->variants()->delete();
        $product->delete();

        return response()->json(['ok' => true]);
    }

    // ─── VARIANT SYNC ────────────────────────────────────
    private function syncVariants(Product $product, array $variants)
    {
        $sentIds = [];

        foreach ($variants as $v) {
            $data = [
                'color'     => $v['color'],
                'size'      => $v['size'],
                'price'     => isset($v['price']) && $v['price'] !== '' ? $v['price'] : null,
                'stock'     => (int) ($v['stock'] ?? 0),
                'is_active' => true,
            ];

            if (!empty($v['id'])) {
                // update existing
                $variant = ProductVariant::where('id', $v['id'])
                    ->where('product_id', $product->id)
                    ->first();
                if ($variant) {
                    $variant->update($data);
                    $sentIds[] = $variant->id;
                }
            } else {
                // create new — skip duplicates silently
                $existing = ProductVariant::where('product_id', $product->id)
                    ->where('color', $v['color'])
                    ->where('size', $v['size'])
                    ->first();

                if ($existing) {
                    $existing->update($data);
                    $sentIds[] = $existing->id;
                } else {
                    $new = $product->variants()->create($data);
                    $sentIds[] = $new->id;
                }
            }
        }

        // dzēš variantus, kas netika sūtīti
        if (!empty($sentIds)) {
            $product->variants()->whereNotIn('id', $sentIds)->delete();
        }
    }

    // ─── ORDERS ──────────────────────────────────────────
    public function orders()
    {
        try {
            $orders = Order::with('user')->orderByDesc('id')->limit(100)->get();
            return response()->json($orders);
        } catch (\Throwable $e) {
            return response()->json([]);
        }
    }

    // ─── UPDATE ORDER STATUS ─────────────────────────────
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:processing,shipped,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json(['ok' => true, 'status' => $order->status]);
    }

    // ─── UPDATE USER ROLE ────────────────────────────────
    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Nevar mainīt savu lomu.'], 403);
        }

        $user->update(['is_admin' => !$user->is_admin]);

        return response()->json(['ok' => true, 'is_admin' => $user->is_admin]);
    }

    // ─── USERS ───────────────────────────────────────────
    public function users()
    {
        $users = User::withCount('orders')->orderByDesc('id')->get();
        return response()->json($users);
    }
}