<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReappliesAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_audit')->create('reapplies', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_start');
            $table->timestamp('date_end')->nullable()->default(null);//for MySQL 5 capability
            $table->integer('log_id');
            $table->integer('customer_id');
            $table->integer('referral_id');
            $table->integer('requested_amount');
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
            $table->timestamp('next_pay_day')->nullable()->default(null);//for MySQL 5 capability
            $table->timestamp('second_pay_day')->nullable()->default(null);//for MySQL 5 capability
            $table->string('user_agent')->nullable();
            $table->boolean('specialist_talk')->default(0);
            $table->string('click_id')->nullable();
            $table->float('risk')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_audit')->dropIfExists('reapplies');
    }
}
