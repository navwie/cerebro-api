<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefferalIdToDecisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('decisions', function (Blueprint $table) {
            $table->integer('referral_id')->after('customer_id')->nullable();
        });

        Schema::connection('mysql_audit')->table('decisions', function (Blueprint $table) {
            $table->integer('referral_id')->after('log_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('decisions', function (Blueprint $table) {
            $table->dropColumn('referral_id');
        });

        Schema::connection('mysql_audit')->table('decisions', function (Blueprint $table) {
            $table->dropColumn('referral_id');
        });
    }
}
