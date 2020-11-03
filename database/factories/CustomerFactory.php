<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Remark;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $remark = Remark::inRandomOrder()->first();

        return [
            'phone' => $this->faker->tollFreePhoneNumber,
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'remark_id' => $remark->id,
        ];
    }
}
