<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->faker->unique()->word,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->text,
            'price' => $this->faker->numberBetween(1000, 10000),
            'trial_price' => $this->faker->numberBetween(100, 1000),
        ];
    }
}
