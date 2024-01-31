<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSsnForEncrypting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('ssn')->change();
            $table->string('ssn_short')->after('ssn');
        });
        Schema::connection('mysql_audit')->table('customers', function (Blueprint $table) {
            $table->string('ssn')->change();
            $table->string('ssn_short')->after('ssn');
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
