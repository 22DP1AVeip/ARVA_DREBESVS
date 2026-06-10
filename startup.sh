#!/bin/bash
php artisan migrate --force
php artisan db:seed --class=ProductSeeder --force
php artisan db:seed --class=CouponSeeder --force
php artisan config:clear
php artisan cache:clear
php artisan tinker --execute="App\Models\User::updateOrCreate(['email'=>'admin@arva.lv'],['name'=>'Admin','password'=>bcrypt('admin123'),'is_admin'=>1,'email_verified_at'=>'2026-01-01 00:00:00']);"
php artisan tinker --execute="\$u=App\Models\User::where('email','admin@arva.lv')->first(); if(\$u) DB::table('point_transactions')->insert(['user_id'=>\$u->id,'points'=>999999,'type'=>'earn','description'=>'Admin bonus','order_id'=>null,'created_at'=>now(),'updated_at'=>now()]);"
php -S 0.0.0.0:3000 -t public