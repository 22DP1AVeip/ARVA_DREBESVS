<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Jeans',
                'price' => 39.99,
                'category' => 'jeans',
                'image_men' => '/bildites/Men_jeans_2.jpg',
                'image_women' => '/bildites/Woman_jeans_2.jpg',
            ],
            [
                'name' => 'T-Shirt',
                'price' => 19.99,
                'category' => 'tshirt',
                'image_men' => '/bildites/Men_tshirt_1.jpg',
                'image_women' => '/bildites/Woman_tshirt_1.jpg',
            ],
            [
                'name' => 'Jacket',
                'price' => 79.99,
                'category' => 'jacket',
                'image_men' => '/bildites/Men_jacket_1.jpg',
                'image_women' => '/bildites/Woman_jacket_1.jpg',
            ],
            [
                'name' => 'Shoes',
                'price' => 59.99,
                'category' => 'shoes',
                'image_men' => '/bildites/Men_shoes_1.jpg',
                'image_women' => '/bildites/Woman_shoes_1.jpg',
            ],
            [
                'name' => 'Hat',
                'price' => 15.99,
                'category' => 'hat',
                'image_men' => '/bildites/Men_hats_1.jpg',
                'image_women' => '/bildites/Woman_hats_1.jpg',
            ],
            [
                'name' => 'Jeans 2',
                'price' => 49.99,
                'category' => 'jeans',
                'image_men' => '/bildites/Men_jeans_1.jpg',
                'image_women' => '/bildites/Woman_jeans_1.jpg',
            ],
            [
                'name' => 'T-Shirt 2',
                'price' => 24.99,
                'category' => 'tshirt',
                'image_men' => '/bildites/Men_tshirt_2.jpg',
                'image_women' => '/bildites/Woman_tshirt_2.jpg',
            ],
            [
                'name' => 'Jacket 2',
                'price' => 99.99,
                'category' => 'jacket',
                'image_men' => '/bildites/Men_jacket_2.jpg',
                'image_women' => '/bildites/Woman_jacket_2.jpg',
            ],
            [
                'name' => 'Shoes 2',
                'price' => 64.99,
                'category' => 'shoes',
                'image_men' => '/bildites/Men_shoes_2.jpg',
                'image_women' => '/bildites/Woman_shoes_2.jpg',
            ],
            [
                'name' => 'Hat 2',
                'price' => 19.99,
                'category' => 'hat',
                'image_men' => '/bildites/Men_hats_2.jpg',
                'image_women' => '/bildites/Woman_hats_2.jpg',
            ],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}
