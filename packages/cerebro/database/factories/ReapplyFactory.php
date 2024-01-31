<?php

namespace Database\Factories;

use App\Models\Reapply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;

class ReapplyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reapply::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customer_id' => 1,
            'referral_id' => 1,
            'requested_amount' => $this->faker->numberBetween(10, 5000),
            'next_pay_day' => $this->faker->dateTimeBetween('first day of january previous year', 'last day of december previous year')->format('Y-m-d'),
            'second_pay_day' => $this->faker->dateTimeBetween('first day of january previous year', 'last day of december previous year')->format('Y-m-d'),
            'specialist_talk' => $this->faker->boolean(),
            'user_agent' => $this->faker->chrome(),
            'created_at' => $this->faker->dateTimeBetween('first day of january previous year', 'last day of december previous year'),
            'reason_for_loan' => $this->faker->randomElement(Reapply::TYPE_REASONS_FOR_LOAN),
            'pay_frequency' => $this->faker->randomElement(Reapply::TYPE_PAY_FREQUENCY),
            'risk' => $this->faker->numberBetween(0, 100),
            'referring_url' => $this->faker->url(),
            'lead_type' => 'payday',
            'click_id' => $this->faker->regexify('[A-Za-z0-9]{20}')
        ];
    }

    /**
     * Creating resource for tests.
     */
    public function test(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'auto_redirect_to_lead' => 0,
            ];
        });
    }
}
