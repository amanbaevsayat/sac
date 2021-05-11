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
            $product->prices()->create([
                'product_id' => $product->id,
                'price' => 3000,
            ]);
        }
    }
}
