<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->truncate();

        $products = [
            // MEN (10)
            ['name'=>'Men Jeans 1','price'=>39.99,'category'=>'jeans','gender'=>'men','image_men'=>'/bildites/Men_jeans_1.jpg','image_women'=>'/bildites/Woman_jeans_1.jpg'],
            ['name'=>'Men Jeans 2','price'=>49.99,'category'=>'jeans','gender'=>'men','image_men'=>'/bildites/Men_jeans_2.jpg','image_women'=>'/bildites/Woman_jeans_2.jpg'],
            ['name'=>'Men T-Shirt 1','price'=>19.99,'category'=>'tshirt','gender'=>'men','image_men'=>'/bildites/Men_tshirt_1.jpg','image_women'=>'/bildites/Woman_tshirt_1.jpg'],
            ['name'=>'Men T-Shirt 2','price'=>24.99,'category'=>'tshirt','gender'=>'men','image_men'=>'/bildites/Men_tshirt_2.jpg','image_women'=>'/bildites/Woman_tshirt_2.jpg'],
            ['name'=>'Men Jacket 1','price'=>79.99,'category'=>'jacket','gender'=>'men','image_men'=>'/bildites/Men_jacket_1.jpg','image_women'=>'/bildites/Woman_jacket_1.jpg'],
            ['name'=>'Men Jacket 2','price'=>99.99,'category'=>'jacket','gender'=>'men','image_men'=>'/bildites/Men_jacket_2.jpg','image_women'=>'/bildites/Woman_jacket_2.jpg'],
            ['name'=>'Men Shoes 1','price'=>59.99,'category'=>'shoes','gender'=>'men','image_men'=>'/bildites/Men_shoes_1.jpg','image_women'=>'/bildites/Woman_shoes_1.jpg'],
            ['name'=>'Men Shoes 2','price'=>64.99,'category'=>'shoes','gender'=>'men','image_men'=>'/bildites/Men_shoes_2.jpg','image_women'=>'/bildites/Woman_shoes_2.jpg'],
            ['name'=>'Men Hat 1','price'=>15.99,'category'=>'hat','gender'=>'men','image_men'=>'/bildites/Men_hats_1.jpg','image_women'=>'/bildites/Woman_hats_1.jpg'],
            ['name'=>'Men Hat 2','price'=>19.99,'category'=>'hat','gender'=>'men','image_men'=>'/bildites/Men_hats_2.jpg','image_women'=>'/bildites/Woman_hats_2.jpg'],

            // WOMEN (10)
            ['name'=>'Women Jeans 1','price'=>39.99,'category'=>'jeans','gender'=>'women','image_men'=>'/bildites/Men_jeans_1.jpg','image_women'=>'/bildites/Woman_jeans_1.jpg'],
            ['name'=>'Women Jeans 2','price'=>49.99,'category'=>'jeans','gender'=>'women','image_men'=>'/bildites/Men_jeans_2.jpg','image_women'=>'/bildites/Woman_jeans_2.jpg'],
            ['name'=>'Women T-Shirt 1','price'=>19.99,'category'=>'tshirt','gender'=>'women','image_men'=>'/bildites/Men_tshirt_1.jpg','image_women'=>'/bildites/Woman_tshirt_1.jpg'],
            ['name'=>'Women T-Shirt 2','price'=>24.99,'category'=>'tshirt','gender'=>'women','image_men'=>'/bildites/Men_tshirt_2.jpg','image_women'=>'/bildites/Woman_tshirt_2.jpg'],
            ['name'=>'Women Jacket 1','price'=>79.99,'category'=>'jacket','gender'=>'women','image_men'=>'/bildites/Men_jacket_1.jpg','image_women'=>'/bildites/Woman_jacket_1.jpg'],
            ['name'=>'Women Jacket 2','price'=>99.99,'category'=>'jacket','gender'=>'women','image_men'=>'/bildites/Men_jacket_2.jpg','image_women'=>'/bildites/Woman_jacket_2.jpg'],
            ['name'=>'Women Shoes 1','price'=>59.99,'category'=>'shoes','gender'=>'women','image_men'=>'/bildites/Men_shoes_1.jpg','image_women'=>'/bildites/Woman_shoes_1.jpg'],
            ['name'=>'Women Shoes 2','price'=>64.99,'category'=>'shoes','gender'=>'women','image_men'=>'/bildites/Men_shoes_2.jpg','image_women'=>'/bildites/Woman_shoes_2.jpg'],
            ['name'=>'Women Hat 1','price'=>15.99,'category'=>'hat','gender'=>'women','image_men'=>'/bildites/Men_hats_1.jpg','image_women'=>'/bildites/Woman_hats_1.jpg'],
            ['name'=>'Women Hat 2','price'=>19.99,'category'=>'hat','gender'=>'women','image_men'=>'/bildites/Men_hats_2.jpg','image_women'=>'/bildites/Woman_hats_2.jpg'],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}
