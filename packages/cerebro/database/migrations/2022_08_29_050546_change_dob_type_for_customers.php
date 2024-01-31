<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDobTypeForCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->date('dob')->nullable()->default(null)->change();//for MySQL 5 capability
        });
        Schema::connection('mysql_audit')->table('customers', function (Blueprint $table) {
            $table->date('dob')->nullable()->default(null)->change();//for MySQL 5 capability
        });
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
