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
        Schema::table('customer_credit_cards', function (Blueprint $table) {
            $table->integer('referral_id')->after('id');
        });
        Schema::connection('mysql_audit')->table('customer_credit_cards', function (Blueprint $table) {
            $table->integer('referral_id')->after('log_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_credit_cards', function (Blueprint $table) {
            $table->dropColumn('referral_id');
        });
        Schema::connection('mysql_audit')->table('customer_credit_cards', function (Blueprint $table) {
            $table->dropColumn('referral_id');
        });
    }
};
