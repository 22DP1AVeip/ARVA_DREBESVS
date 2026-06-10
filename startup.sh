#!/bin/bash
php artisan migrate --force --ignore-errors 2>/dev/null || true
php artisan migrate --force
php artisan db:seed --class=ProductSeeder --force
php artisan serve --host=0.0.0.0 --port=3000