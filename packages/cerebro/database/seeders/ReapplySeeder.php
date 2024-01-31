<?php

namespace Database\Seeders;

use App\Models\Reapply;
use Illuminate\Database\Seeder;

class ReapplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Reapply::factory()->times(1)->create();
    }
}
