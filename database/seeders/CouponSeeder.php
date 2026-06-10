<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('coupons')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::table('coupons')->insert([
            ['name' => '5% Atlaide',  'tier' => 'bronze',   'discount_percent' => 5,  'points_required' => 500,  'created_at' => now(), 'updated_at' => now()],
            ['name' => '10% Atlaide', 'tier' => 'silver',   'discount_percent' => 10, 'points_required' => 1000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '25% Atlaide', 'tier' => 'gold',     'discount_percent' => 25, 'points_required' => 2500, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '50% Atlaide', 'tier' => 'platinum', 'discount_percent' => 50, 'points_required' => 5000, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
