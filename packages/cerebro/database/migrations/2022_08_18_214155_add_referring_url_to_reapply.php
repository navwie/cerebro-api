<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferringUrlToReapply extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reapplies', function (Blueprint $table) {
            $table->string('referring_url')->nullable()->after('user_agent');
        });
        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->string('referring_url')->nullable()->after('user_agent');
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
            //
        });
    }
}
