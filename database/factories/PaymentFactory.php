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
        $customer = Customer::inRandomOrder()->first();
        $subscription = Subscription::inRandomOrder()->first();
        $types = [
            'cloudpayments',
            'kaspibank',
            'sberbank',
        ];
        
        $intervals = [
            'Day',
            'Week',
            'Month',
        ];

        return [
            'subscription_id' => $subscription->id,
            'customer_id' => $customer->id,
            'type' => $types[array_rand($types)],
            'slug' => Str::uuid(),
            'amount' => $this->faker->numberBetween(1000, 10000),
            'status' => array_rand(Payment::STATUSES),
            'recurrent' => rand(0,1), // Для рекуррентных платежей
            'start_date' => $this->faker->dateTimeBetween('-2 month', 'now'), // Для рекуррентных платежей
            'interval' => $intervals[array_rand($intervals)], // Для рекуррентных платежей
            'period' => rand(1,2), // Для рекуррентных платежей
        ];
    }
}
