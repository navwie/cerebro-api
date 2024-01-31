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
        Schema::create('visitors_card', function (Blueprint $table) {
            $table->id();
            $table->integer('referral_id');
            $table->string('source_url')->nullable();
            $table->string('ip_address');
            $table->string('click_id')->nullable();
            $table->json('sub_ids')->nullable();
            $table->integer('visits_amount')->default('0');
            $table->string('url')->nullable();
            $table->text('user_agent');
            $table->date('date');
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
        Schema::dropIfExists('visitors_card');
    }
};
