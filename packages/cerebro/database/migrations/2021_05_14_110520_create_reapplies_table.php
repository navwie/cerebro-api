<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReappliesTable extends Migration
{
    protected $connection = 'mysql';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reapplies', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('referral_id');
            $table->integer('requested_amount');
            $table->enum('reason_for_loan', [
                'Debt Consolidation',
                'Emergency Situation',
                'Auto Repair',
                'Auto Purchase',
                'Moving',
                'Home Improvement',
                'Medical',
                'Business',
                'Vacation',
                'Taxes',
                'Rent or Mortgage',
                'Wedding',
                'Major Purchase',
                'Student Loan Refinance',
                'Credit Card Consolidation',
                'Other'
            ])->nullable();
            $table->enum('pay_frequency', ['Weekly', 'Every 2 Weeks', 'Twice A Month', 'Monthly']);
            $table->timestamp('next_pay_day')->nullable()->default(null);//for MySQL 5 capability
            $table->timestamp('second_pay_day')->nullable()->default(null);//for MySQL 5 capability
            $table->boolean('specialist_talk');
            $table->string('user_agent')->nullable();
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
        Schema::dropIfExists('reapplies');
    }
}
