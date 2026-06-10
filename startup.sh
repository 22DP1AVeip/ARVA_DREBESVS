#!/bin/bash
php artisan migrate --force
php artisan db:seed --class=ProductSeeder --force
php artisan db:seed --class=CouponSeeder --force
php artisan config:clear
php artisan cache:clear
php artisan tinker --execute="App\Models\User::where('email','admin@arva.lv')->first()->tap(function(\$u){ DB::table('point_transactions')->insert(['user_id'=>\$u->id,'points'=>999999,'type'=>'earn','description'=>'Admin bonus','order_id'=>null,'created_at'=>now(),'updated_at'=>now()]); });"
php -S 0.0.0.0:3000 -t public