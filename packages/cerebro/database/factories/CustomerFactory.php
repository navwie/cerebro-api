<?php

namespace Database\Factories;

use App\Models\Customer;
use Carbon\Carbon;
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
        $ssn = $this->faker->randomNumber(9, true);
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->freeEmail(),
            'dob' => $this->faker->dateTimeBetween('-30 years', '-18 years')->format('Y-m-d'),
            'ssn' => openssl_encrypt($ssn, 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv')),
            'ssn_short' => openssl_encrypt(substr($ssn, -4), 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv')),
            'ip_address' => $this->faker->ipv4(),
            'address' => $this->faker->streetAddress(),
            'state' => $this->faker->regexify('[A-Z]{2}'),
            'city' => $this->faker->city(),
            'zip' => $this->faker->bothify('#####'),
            'home_phone' => '5812312312',
            'dl_state' => $this->faker->regexify('[A-Z]{2}'),
            'dl_number' => $this->faker->word(10),
            'armed_forces' => $this->faker->boolean(),
            'credit_score' => $this->faker->numberBetween(1, 999),
            'contact_time' => $this->faker->randomElement(Customer::TYPE_CONTACT_TIME),
            'rent_or_own' => $this->faker->boolean(),
            'address_month' => $this->faker->numberBetween(0, 860),
            'citizenship' => $this->faker->boolean(),
            'income_type' => $this->faker->randomElement(array_merge_recursive(Customer::TYPE_INCOME_LEAD,Customer::TYPE_INCOME_PERSONAL)),
            'emp_time' => $this->faker->numberBetween(0, 860),
            'emp_name' => $this->faker->company(),
            'emp_phone' => '5812312312',
            'job_title' => $this->faker->jobTitle(),
            'own_car' => $this->faker->boolean(),
            'debt_amount' => 0,
            'submit_sms' => 1,
        ];
    }


    /**
     * Creating resource for tests.
     */
    public function test(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'ssn' => $this->faker->randomNumber(9, true)
            ];
        });
    }
}
