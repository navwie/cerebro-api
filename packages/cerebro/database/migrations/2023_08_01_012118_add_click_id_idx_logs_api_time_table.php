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
        Schema::table('logs_api_time', function (Blueprint $table) {
            $table->index(['click_id'], 'logs_api_time_idx_click_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs_api_time', function (Blueprint $table) {
            $table->dropIndex('logs_api_time_idx_click_id');
        });
    }
};
