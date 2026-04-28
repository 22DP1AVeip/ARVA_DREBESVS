<?php
namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::updateOrCreate(
            ['user_id' => $request->user()->id, 'product_id' => $product->id],
            ['rating' => $data['rating'], 'comment' => $data['comment'] ?? null]
        );

        return back()->with('success', 'Atsauksme saglabāta!');
    }

    public function destroy(Request $request, Product $product)
    {
        Review::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Atsauksme dzēsta.');
    }
}
