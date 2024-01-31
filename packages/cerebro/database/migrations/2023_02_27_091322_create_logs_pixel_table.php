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
        Schema::create('logs_pixel', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->integer('reapply_id');
            $table->integer('customer_id');
            $table->integer('decision_id')->nullable();
            $table->string('action_type');
            $table->string('click_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('referring_url');
            $table->text('user_agent');
            $table->string('ip_address');
            $table->text('sent_pixel')->nullable();
            $table->text('response_pixel')->nullable();
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
        Schema::dropIfExists('logs_pixel');
    }
};
