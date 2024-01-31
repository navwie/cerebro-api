<?php

namespace Database\Seeders;

use App\Models\ReapplyAudit;
use Illuminate\Database\Seeder;

class ReapplyAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReapplyAudit::factory()->times(5000)->create();
    }
}
