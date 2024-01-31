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
            $table->string('action_type')->nullable()->after('flow_id');
        });

        Schema::table('reapplies', function (Blueprint $table) {
            $table->string('action_type')->nullable()->after('flow_id');
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->string('action_type')->nullable()->after('flow_id');
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
            $table->dropColumn('action_type');
        });

        Schema::table('reapplies', function (Blueprint $table) {
            $table->dropColumn('action_type');
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->dropColumn('action_type');
        });
    }
};
