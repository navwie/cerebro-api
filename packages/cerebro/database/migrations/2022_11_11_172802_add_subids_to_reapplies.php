<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubidsToReapplies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reapplies', function (Blueprint $table) {
            $table->json('sub_ids')->nullable()->after('click_id');
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->json('sub_ids')->nullable()->after('click_id');
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
