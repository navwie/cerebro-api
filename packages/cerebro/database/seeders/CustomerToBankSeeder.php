<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerToBank;

class CustomerToBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerToBank::factory()->times(1)->create();
    }
}
