<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerAudit;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerAuditFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerAudit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ssn = $this->faker->randomNumber(9, true);
        return [
            'log_id' => $this->faker->randomNumber(2),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'dob' => $this->faker->dateTimeBetween('-30 years', '-10 years')->format('Y-m-d'),
            'ssn' => openssl_encrypt($ssn, 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv')),
            'ssn_short' => openssl_encrypt(substr($ssn, -4), 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv')),
            'ip_address' => $this->faker->ipv4(),
            'address' => $this->faker->streetAddress(),
            'state' => $this->faker->regexify('[A-Z]{2}'),
            'city' => $this->faker->city(),
            'zip' => $this->faker->bothify('#####-####'),
            'home_phone' => $this->faker->regexify('[0-9]{10}'),
            'cell_phone' =>  $this->faker->regexify('[0-9]{10}'),
            'dl_state' => $this->faker->regexify('[A-Z]{2}'),
            'dl_number' => $this->faker->word(10),
            'armed_forces' => $this->faker->boolean(),
            'credit_score' => $this->faker->numberBetween(0, 5),
            'contact_time' => $this->faker->randomElement(Customer::TYPE_CONTACT_TIME),
            'rent_or_own' => $this->faker->boolean(),
            'address_month' => $this->faker->numberBetween(0, 860),
            'citizenship' => $this->faker->boolean(),
            'income_type' => $this->faker->randomElement(array_merge_recursive(Customer::TYPE_INCOME_LEAD,Customer::TYPE_INCOME_PERSONAL)),
            'emp_time' => $this->faker->numberBetween(0, 860),
            'emp_name' => $this->faker->company(),
            'emp_phone' => $this->faker->regexify('[0-9]{10}'),
            'job_title' => $this->faker->jobTitle(),
            'own_car' => $this->faker->boolean(),
            'debt_amount' => 0,
            'submit_sms' => 1
        ];
    }
}
