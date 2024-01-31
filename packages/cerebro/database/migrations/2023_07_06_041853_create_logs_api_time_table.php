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
        Schema::create('logs_api_time', function (Blueprint $table) {
            $table->id();
            $table->text('user_agent')->nullable();
            $table->string('click_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('referring_url')->nullable();
            $table->string('action_type');
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
        Schema::dropIfExists('logs_api_time');
    }
};
