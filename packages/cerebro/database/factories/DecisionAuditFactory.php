<?php

namespace Database\Factories;

use App\Models\CustomerAudit;
use App\Models\DecisionAudit;
use App\Models\ReapplyAudit;
use Illuminate\Database\Eloquent\Factories\Factory;

class DecisionAuditFactory extends Factory
{

    protected $model = DecisionAudit::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        $response = $this->faker->randomElement(['sold', 'reject', 'error']);
        $message = match ($response) {
            'sold' => 'ACCEPTED',
            'reject' => 'REJECTED',
            'error' => 'ERROR',
        };
        $reapply = ReapplyAudit::factory()->create();
        return [
            'customer_id' => CustomerAudit::factory(),
            'log_id' => $this->faker->randomNumber(2),
            'decision_id' => $this->faker->randomNumber(3),
            'reapply_id' => $reapply->id,
            'referral_id' => $reapply->referral_id,
            'redirected' => $this->faker->boolean,
            'decision_status' => $response,
            'decision_message' => $message,
            'decision_price' => $response == 'sold' ? $this->faker->randomFloat(2,0,10) : 0,
            'decision_redirect' => $response == 'sold' ? 'https://dnmstats.com/personal/lead/sign/0' : '',
            'created_at' => $this->faker->dateTimeBetween('first day of january previous year', 'last day of december previous year')
        ];
    }
}
