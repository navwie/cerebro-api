<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_audit')->create('customers', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_start');
            $table->timestamp('date_end')->nullable()->default(null);//for MySQL 5 capability
            $table->integer('log_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->timestamp('dob')->nullable()->default(null);//for MySQL 5 capability
            $table->integer('ssn');
            $table->string('ip_address');
            $table->string('address');
            $table->string('state');
            $table->string('city');
            $table->string('zip');
            $table->string('home_phone');
            $table->string('cell_phone')->nullable();
            $table->string('dl_state');
            $table->string('dl_number');
            $table->tinyInteger('armed_forces');
            $table->integer('credit_score')->nullable();
            $table->enum('contact_time', ['anytime', 'morning', 'afternoon', 'evening']);
            $table->tinyInteger('rent_or_own');
            $table->integer('address_month');
            $table->tinyInteger('citizenship');
            $table->enum('income_type', [
                'job income',
                'self employed',
                'benefits',
                'pension',
                'social security',
                'disability income',
                'military',
                'other',
            ]);
            $table->integer('debt_amount')->default(0);
            $table->integer('emp_time');
            $table->string('emp_name');
            $table->string('emp_phone');
            $table->string('job_title');
            $table->tinyInteger('own_car')->nullable();
            $table->boolean('submit_sms')->default(0);
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
        Schema::connection('mysql_audit')->dropIfExists('customers');
    }
}
