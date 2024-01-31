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
        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->boolean('request_id_mark')->default(0)->after('specialist_talk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->dropColumn('request_id_mark');
        });
    }
};
