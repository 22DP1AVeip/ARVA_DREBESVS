#!/bin/bash
php artisan migrate:fresh --force
php artisan db:seed --class=ProductSeeder --force
php artisan key:generate --force
php artisan config:clear
php artisan cache:clear
php artisan serve --host=0.0.0.0 --port=3000
