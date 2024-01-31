<?php

namespace Database\Seeders;

use App\Models\DecisionAudit;
use Illuminate\Database\Seeder;

class DecisionAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DecisionAudit::factory()->times(5000)->create();
    }
}
