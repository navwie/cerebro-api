<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDecisionAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_audit')->create('decisions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_start');
            $table->timestamp('date_end')->nullable()->default(null);//for MySQL 5 capability
            $table->integer('log_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('reapply_id');
            $table->integer('decision_id');
            $table->string('decision_status');
            $table->string('decision_message');
            $table->decimal('decision_price', 15,2);
            $table->string('decision_redirect');
            $table->boolean('redirected')->default(false);
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
        Schema::connection('mysql_audit')->drop('decisions');
    }
}
