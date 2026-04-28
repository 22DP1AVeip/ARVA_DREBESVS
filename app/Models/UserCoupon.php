<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    protected $fillable = ['user_id', 'coupon_id', 'code', 'used_at'];
    protected $casts = ['used_at' => 'datetime'];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
