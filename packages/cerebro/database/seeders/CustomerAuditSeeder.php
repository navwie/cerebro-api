<?php

namespace Database\Seeders;

use App\Models\CustomerAudit;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerAudit::factory()->times(1000)->create();
    }
}
