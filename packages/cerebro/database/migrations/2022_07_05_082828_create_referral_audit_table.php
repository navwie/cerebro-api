<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_audit')->create('referral', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_start');
            $table->timestamp('date_end')->nullable()->default(null);//for MySQL 5 capability
            $table->integer('log_id');
            $table->string('channel_id');
            $table->string('password');
            $table->string('aff_sub_id')->nullable();
            $table->string('aff_sub_id2')->nullable();
            $table->string('aff_sub_id3')->nullable();
            $table->string('aff_sub_id4')->nullable();
            $table->string('aff_sub_id5')->nullable();
            $table->string('referring_url')->nullable();
            $table->decimal('min_price')->nullable();
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
        Schema::connection('mysql_audit')->dropIfExists('referral');
    }
}
