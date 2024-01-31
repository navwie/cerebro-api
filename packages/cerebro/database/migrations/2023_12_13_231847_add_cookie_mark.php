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
            $table->boolean('cookie_mark')->after('action_type');
        });

        Schema::table('reapplies', function (Blueprint $table) {
            $table->boolean('cookie_mark')->default(0)->after('imported_mark');
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->boolean('cookie_mark')->default(0)->after('denied_mark');
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
            $table->dropColumn('cookie_mark');
        });

        Schema::table('reapplies', function (Blueprint $table) {
            $table->dropColumn('cookie_mark');
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->dropColumn('cookie_mark');
        });
    }
};
