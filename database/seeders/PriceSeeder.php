<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::get();

        foreach ($products as $product) {
            for ($x = 0; $x <= 2; $x++) {
                $product->prices()->create([
                    'product_id' => $product->id,
                    'is_active' => true,
                    'price' => rand(30, 80) * 100,
                ]);
            }
        }
    }
}
