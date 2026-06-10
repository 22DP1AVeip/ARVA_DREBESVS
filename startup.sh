#!/bin/bash
php artisan migrate --force
php artisan db:seed --class=ProductSeeder --force
php artisan config:clear
php artisan cache:clear
php -S 0.0.0.0:3000 -t public