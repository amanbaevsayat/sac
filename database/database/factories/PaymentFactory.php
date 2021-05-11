<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $subscription = Subscription::inRandomOrder()->first();
        
        $intervals = [
            'Day',
            'Week',
            'Month',
        ];

        return [
            'subscription_id' => $subscription->id,
            'customer_id' => $subscription->customer->id,
            'type' => array_rand(Subscription::PAYMENT_TYPE),
            'slug' => Str::uuid(),
            'quantity' => $this->faker->numberBetween(1, 3),
            'status' => array_rand(Payment::STATUSES),
            'recurrent' => rand(0,1), // Для рекуррентных платежей
            'start_date' => $this->faker->dateTimeBetween('-2 month', 'now'), // Для рекуррентных платежей
            'interval' => $intervals[array_rand($intervals)], // Для рекуррентных платежей
            'period' => rand(1,2), // Для рекуррентных платежей
        ];
    }
}
