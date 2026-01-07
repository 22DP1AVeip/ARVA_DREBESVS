<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index()
    {
        // Vienkārši atver checkout lapu.
        // Cart tiek rādīts no Inertia shared props (page.props.cart) Vue pusē.
        return \Inertia\Inertia::render('CheckoutView');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:190'],
            'city' => ['required', 'string', 'max:120'],
            'postcode' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:120'],
            'payment_method' => ['required', 'in:card,cod'],
        ]);

        return back()->with('success', 'Checkout nosūtīts (placeholder).');
    }
}