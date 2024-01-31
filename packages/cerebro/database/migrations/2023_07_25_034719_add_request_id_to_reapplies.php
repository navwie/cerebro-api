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
        Schema::table('reapplies', function (Blueprint $table) {
            $table->string('request_id')->nullable()->after('referral_id');
        });
        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->string('request_id')->nullable()->after('referral_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reapplies', function (Blueprint $table) {
            $table->dropColumn('request_id');
        });
        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->dropColumn('request_id');
        });
    }
};
