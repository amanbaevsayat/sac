<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $customer = Customer::inRandomOrder()->first();
        $product = Product::inRandomOrder()->first();
        $statuses = Subscription::STATUSES;

        $pausedAt = [
            $this->faker->dateTimeBetween('-2 week', 'now'),
            null,
        ];

        return [
            'started_at' => $this->faker->dateTimeBetween('-2 month', 'now'),
            'paused_at' => $pausedAt[array_rand($pausedAt)],
            'ended_at' => $this->faker->dateTimeBetween('-1 week', '+3 month'),
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'amount' => $product->price,
            'description' => 'Подписка ' . $product->title,
            'status' => array_rand($statuses),
        ];
    }
}
