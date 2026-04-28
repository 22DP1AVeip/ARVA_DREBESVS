<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['name', 'tier', 'discount_percent', 'points_required'];

    public function userCoupons()
    {
        return $this->hasMany(UserCoupon::class);
    }
}
