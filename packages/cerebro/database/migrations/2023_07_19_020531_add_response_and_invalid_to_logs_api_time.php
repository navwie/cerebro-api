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
            $table->text('request')->nullable()->after('action_type');
            $table->text('response')->nullable()->after('request');
            $table->boolean('invalid')->default(0)->after('response');
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
            $table->dropColumn('request');
            $table->dropColumn('response');
            $table->dropColumn('invalid');
        });
    }
};
