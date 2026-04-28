<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function show(Request $request, $id)
    {
        $view = $request->query('view', 'men');

        $product = Product::with(['variants' => function ($q) {
            $q->where('is_active', true)
              ->orderBy('color')
              ->orderBy('size');
        }])->findOrFail($id);

        $reviews = Review::with('user:id,name')
            ->where('product_id', $product->id)
            ->latest()
            ->get()
            ->map(fn($r) => [
                'id'         => $r->id,
                'rating'     => $r->rating,
                'comment'    => $r->comment,
                'user_name'  => $r->user->name,
                'user_id'    => $r->user_id,
                'created_at' => $r->created_at->format('d.m.Y'),
            ]);

        $userReview = $request->user()
            ? $reviews->firstWhere('user_id', $request->user()->id)
            : null;

        $avgRating = $reviews->count() ? round($reviews->avg('rating'), 1) : null;

        return Inertia::render('ProductShow', [
            'product'    => $product,
            'view'       => $view,
            'variants'   => $product->variants->map(fn ($v) => [
                'id'    => $v->id,
                'size'  => $v->size,
                'color' => $v->color,
                'price' => $v->price,
            ])->values(),
            'reviews'    => $reviews,
            'userReview' => $userReview,
            'avgRating'  => $avgRating,
        ]);
    }
}
