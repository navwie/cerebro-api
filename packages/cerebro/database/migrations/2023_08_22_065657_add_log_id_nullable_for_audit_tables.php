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
        Schema::connection('mysql_audit')->table('banks', function (Blueprint $table) {
            $table->integer('log_id')->nullable()->default(null)->change();
        });

        Schema::connection('mysql_audit')->table('customer_credit_cards', function (Blueprint $table) {
            $table->integer('log_id')->nullable()->default(null)->change();
        });

        Schema::connection('mysql_audit')->table('customers', function (Blueprint $table) {
            $table->integer('log_id')->nullable()->default(null)->change();
        });

        Schema::connection('mysql_audit')->table('customers_to_banks', function (Blueprint $table) {
            $table->integer('log_id')->nullable()->default(null)->change();
        });

        Schema::connection('mysql_audit')->table('decisions', function (Blueprint $table) {
            $table->integer('log_id')->nullable()->default(null)->change();
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->integer('log_id')->nullable()->default(null)->change();
        });

        Schema::connection('mysql_audit')->table('referral', function (Blueprint $table) {
            $table->integer('log_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
