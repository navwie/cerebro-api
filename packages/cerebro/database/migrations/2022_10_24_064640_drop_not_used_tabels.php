<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropNotUsedTabels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('email_template');
        Schema::dropIfExists('example');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('folder');
        Schema::dropIfExists('form');
        Schema::dropIfExists('form_field');
        Schema::dropIfExists('media');
        Schema::dropIfExists('menu_role');
        Schema::dropIfExists('menulist');
        Schema::dropIfExists('notes');
        Schema::dropIfExists('referral');
        Schema::dropIfExists('status');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
