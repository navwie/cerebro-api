<?php

namespace Database\Factories;

use App\Models\CustomerToBank;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;
use App\Models\Bank;

class CustomerToBankFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerToBank::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'bank_id' => 1,
            'customer_id' => 1,
            'account_number' => $this->faker->regexify('[0-9]{10}'),
            'bank_months' => $this->faker->numberBetween(0, 860),
            'net_month_income' => $this->faker->numberBetween(1, 5000),
            'direct_deposit' => $this->faker->boolean(),
            'account_type' => $this->faker->randomElement(CustomerToBank::TYPE_ACCOUNT),
        ];
    }
}
