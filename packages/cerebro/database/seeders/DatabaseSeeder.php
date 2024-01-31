<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersAndNotesSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(BankSeeder::class);
        $this->call(CustomerToBankSeeder::class);
        $this->call(VisitorSeeder::class);
        $this->call(ReapplySeeder::class);
        $this->call(DecisionAuditSeeder::class);
    }
}
