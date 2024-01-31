<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersToBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers_to_banks', function (Blueprint $table) {
            $table->id();
            $table->integer('bank_id');
            $table->integer('customer_id');
            $table->enum('account_type', ['Checking Account', 'Savings Account', 'Checking', 'Savings']);
            $table->string('routing_number');
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
        Schema::dropIfExists('customers_to_banks');
    }
}
