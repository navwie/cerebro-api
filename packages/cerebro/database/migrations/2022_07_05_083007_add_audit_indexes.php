<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuditIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_audit')->table('customers', function (Blueprint $table) {
            $table->index(['created_at', 'updated_at'], 'timestamp_idx');
        });

        Schema::connection('mysql_audit')->table('decisions', function (Blueprint $table) {
            $table->index(['created_at', 'updated_at'], 'timestamp_idx');
            $table->index('customer_id', 'customer_idx');
            $table->index('reapply_id', 'reapply_idx');
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->index(['created_at', 'updated_at'], 'timestamp_idx');
            $table->index('customer_id', 'customer_idx');
            $table->index('referral_id', 'referral_idx');
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
