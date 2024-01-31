<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadTypeReapplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->string('lead_type')->after('requested_amount')->nullable();
        });

        Schema::table('reapplies', function (Blueprint $table){
            $table->string('lead_type')->after('requested_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
