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
            $table->integer('flow_id')->nullable()->change();
        });

        Schema::table('reapplies', function (Blueprint $table) {
            $table->integer('flow_id')->nullable()->change();
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->integer('flow_id')->nullable()->change();
        });

        Schema::connection('mysql_audit')->table('decisions', function (Blueprint $table) {
            $table->index('hash_id', 'decisions_hash_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_audit')->table('decisions', function (Blueprint $table) {
            $table->dropIndex('decisions_hash_id_index');
        });
    }
};
