<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get(['id','status','total','created_at']);

        return Inertia::render('Profile/OrdersIndex', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, Order $order)
    {
        // drošība: lai svešs users nevar redzēt citu order
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['items']);

        return Inertia::render('Profile/OrderShow', [
            'order' => $order,
        ]);
    }
}
