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
        Schema::connection('mysql_audit')->create('customer_credit_cards', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_start');
            $table->timestamp('date_end');
            $table->integer('log_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('state');
            $table->text('user_agent');
            $table->string('ip_address');
            $table->string('referring_url');
            $table->string('click_id')->nullable();
            $table->json('sub_ids')->nullable();
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
        Schema::connection('mysql_audit')->dropIfExists('customer_credit_cards');
    }
};
