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
        Schema::create('customer_states', function (Blueprint $table) {
            $table->id();
            $table->integer('referral_id');
            $table->string('referral_url');
            $table->string('email');
            $table->string('click_id')->nullable();
            $table->json('state');
            $table->string('token');
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
        Schema::dropIfExists('customer_states');
    }
};
