<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersToBanksAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_audit')->create('customers_to_banks', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_start');
            $table->timestamp('date_end')->nullable()->default(null);//for MySQL 5 capability
            $table->integer('log_id');
            $table->integer('bank_id');
            $table->integer('customer_id');
            $table->enum('account_type', ['checking', 'savings']);
            $table->string('account_number');
            $table->integer('bank_months');
            $table->string('net_month_income');
            $table->tinyInteger('direct_deposit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_audit')->dropIfExists('customers_to_banks');
    }
}
