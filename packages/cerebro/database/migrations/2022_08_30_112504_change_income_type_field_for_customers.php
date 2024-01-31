<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIncomeTypeFieldForCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE customers MODIFY COLUMN income_type enum(
                'job income',
                'employed',
                'self employed',
                'benefits',
                'pension',
                'social security',
                'disability',
                'disability income',
                'military',
                'other'
            ) NOT NULL");
        DB::connection('mysql_audit')->statement("ALTER TABLE customers MODIFY COLUMN income_type enum(
                'job income',
                'employed',
                'self employed',
                'benefits',
                'pension',
                'social security',
                'disability',
                'disability income',
                'military',
                'other'
            ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
