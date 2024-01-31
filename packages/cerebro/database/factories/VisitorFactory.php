<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $clicks = $this->faker->numberBetween(1, 10);
        return [
            'referral_id' => $this->faker->numberBetween(1, 10),
            'ip_address' => hash('sha512',$this->faker->ipv4()),
            'clicks_amount' => $clicks,
            'visits_amount' => $this->faker->numberBetween($clicks, $clicks + $this->faker->numberBetween(1,5)),
            'user_agent' => $this->faker->userAgent(),
            'url' => $this->faker->url(),
            'date' => $this->faker->dateTimeBetween('first day of january previous year', 'last day of december previous year'),
            'created_at' => $this->faker->dateTimeBetween('first day of january previous year', 'last day of december previous year')
        ];
    }
}
