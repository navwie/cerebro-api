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
        Schema::table('visitors', function (Blueprint $table) {
            $table->integer('flow_id')->default(1)->after('ip_address');
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->integer('flow_id')->default(1)->after('server_id');
        });

        Schema::table('reapplies', function (Blueprint $table) {
            $table->integer('flow_id')->default(1)->after('request_id');
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->integer('flow_id')->default(1)->after('request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn('flow_id');
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('flow_id');
        });

        Schema::table('reapplies', function (Blueprint $table) {
            $table->dropColumn('flow_id');
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->dropColumn('flow_id');
        });
    }
};
