<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_audit')->table('customers', function (Blueprint $table) {
            $table->date('date_start')->nullable()->default(null)->change();//for MySQL 5 capability
            $table->date('date_end')->nullable()->default(null)->change();//for MySQL 5 capability
        });

        Schema::connection('mysql_audit')->table('banks', function (Blueprint $table) {
            $table->date('date_start')->nullable()->default(null)->change();//for MySQL 5 capability
            $table->date('date_end')->nullable()->default(null)->change();//for MySQL 5 capability
        });

        Schema::connection('mysql_audit')->table('customers_to_banks', function (Blueprint $table) {
            $table->date('date_start')->nullable()->default(null)->change();//for MySQL 5 capability
            $table->date('date_end')->nullable()->default(null)->change();//for MySQL 5 capability
        });

        Schema::connection('mysql_audit')->table('decisions', function (Blueprint $table) {
            $table->date('date_start')->nullable()->default(null)->change();//for MySQL 5 capability
            $table->date('date_end')->nullable()->default(null)->change();//for MySQL 5 capability
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->date('date_start')->nullable()->default(null)->change();//for MySQL 5 capability
            $table->date('date_end')->nullable()->default(null)->change();//for MySQL 5 capability
        });

        Schema::connection('mysql_audit')->table('referral', function (Blueprint $table) {
            $table->date('date_start')->nullable()->default(null)->change();//for MySQL 5 capability
            $table->date('date_end')->nullable()->default(null)->change();//for MySQL 5 capability
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('date', function (Blueprint $table) {
            //
        });
    }
};
