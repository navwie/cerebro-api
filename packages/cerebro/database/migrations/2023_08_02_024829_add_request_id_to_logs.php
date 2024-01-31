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
            $table->string('request_id')->nullable()->after('user_agent');
        });

        Schema::table('logs_pixel', function (Blueprint $table) {
            $table->string('request_id')->nullable()->after('action_type');
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
            $table->dropColumn('request_id');
        });
        Schema::table('logs_pixel', function (Blueprint $table) {
            $table->dropColumn('request_id');
        });
    }
};
