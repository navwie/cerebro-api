<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostingDataToForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('post_back_amount')->nullable();
            $table->integer('personal_min_req')->nullable();
            $table->string('personal_channel_id')->nullable();
            $table->string('personal_password')->nullable();
            $table->string('lead_channel_id')->nullable();
            $table->string('lead_password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
