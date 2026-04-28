<?php
namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $allCoupons = Coupon::orderBy('points_required')->get();

        $myCoupons = UserCoupon::with('coupon')
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(fn($uc) => [
                'id'               => $uc->id,
                'code'             => $uc->code,
                'discount_percent' => $uc->coupon->discount_percent,
                'name'             => $uc->coupon->name,
                'tier'             => $uc->coupon->tier,
                'used_at'          => $uc->used_at?->format('d.m.Y'),
                'is_used'          => !is_null($uc->used_at),
            ]);

        return Inertia::render('Profile/Coupons', [
            'points'     => $user->points,
            'allCoupons' => $allCoupons,
            'myCoupons'  => $myCoupons,
        ]);
    }

    public function redeem(Request $request, Coupon $coupon)
    {
        $user = $request->user();

        if ($user->points < $coupon->points_required) {
            return back()->withErrors(['points' => 'Nav pietiekami daudz punktu.']);
        }

        $code = strtoupper('ARVA-' . Str::random(8));

        UserCoupon::create([
            'user_id'   => $user->id,
            'coupon_id' => $coupon->id,
            'code'      => $code,
        ]);

        $spent = $coupon->points_required;
        $user->decrement('points', $spent);

        PointTransaction::create([
            'user_id'     => $user->id,
            'points'      => -$spent,
            'type'        => 'spend',
            'description' => "Izmantoti punkti: {$coupon->name}",
        ]);

        return back()->with('success', "Kupons {$code} aktivizēts!");
    }
}
