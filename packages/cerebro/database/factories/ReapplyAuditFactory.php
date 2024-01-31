<?php

namespace Database\Factories;

use App\Models\Reapply;
use App\Models\ReapplyAudit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReapplyAuditFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReapplyAudit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'log_id' => $this->faker->randomNumber(2),
            'customer_id' => $this->faker->numberBetween(1, 10),
            'referral_id' => $this->faker->numberBetween(1, 10),
            'requested_amount' => $this->faker->numberBetween(10, 5000),
            'reason_for_loan' => $this->faker->randomElement(Reapply::TYPE_REASONS_FOR_LOAN),
            'pay_frequency' => $this->faker->randomElement(Reapply::TYPE_PAY_FREQUENCY),
            'next_pay_day' => $this->faker->dateTimeBetween('first day of january previous year', 'last day of december previous year')->format('Y-m-d'),
            'second_pay_day' => $this->faker->dateTimeBetween('first day of january previous year', 'last day of december previous year')->format('Y-m-d'),
            'user_agent' => $this->faker->chrome(),
            'risk' => $this->faker->numberBetween(0, 100),
            'specialist_talk' => $this->faker->boolean(),
            'created_at' => $this->faker->dateTimeBetween('first day of january previous year', 'last day of december previous year')
        ];
    }
}
