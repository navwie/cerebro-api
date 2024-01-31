<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnumsChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('paid_type');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('paid_type', ['direct deposit', 'paper check']);
        });
        Schema::table('customers_to_banks', function (Blueprint $table) {
            $table->dropColumn('account_type');
        });
        Schema::table('customers_to_banks', function (Blueprint $table) {
            $table->enum('account_type', ['checking', 'savings']);
        });
        Schema::table('reapplies', function (Blueprint $table) {
            $table->dropColumn('reason_for_loan');
            $table->dropColumn('pay_frequency');
        });
        Schema::table('reapplies', function (Blueprint $table) {
            $table->enum('reason_for_loan', [
                'debt consolidation',
                'emergency situation',
                'auto repair',
                'auto purchase',
                'moving',
                'home improvement',
                'medical',
                'business',
                'vacation',
                'taxes',
                'rent or mortgage',
                'wedding',
                'major purchase',
                'student loan refinance',
                'credit card consolidation',
                'other'
            ]);
            $table->enum('pay_frequency', [
                'weekly',
                'every 2 weeks',
                'twice a month',
                'monthly'
            ]);
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
