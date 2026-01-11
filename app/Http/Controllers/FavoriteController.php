<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $favoriteIds = Favorite::query()
            ->where('user_id', $userId)
            ->pluck('product_id')
            ->toArray();

        $products = Product::query()
            ->whereIn('id', $favoriteIds)
            ->get();

        return Inertia::render('Profile/Favorites', [
            'favorites' => $favoriteIds,
            'products' => $products,
        ]);
    }

    public function toggle(Request $request, $productId)
    {
        $userId = $request->user()->id;

        $exists = Favorite::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            Favorite::query()
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->delete();
        } else {
            Product::query()->findOrFail($productId);

            Favorite::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
        }

        return back();
    }
}
