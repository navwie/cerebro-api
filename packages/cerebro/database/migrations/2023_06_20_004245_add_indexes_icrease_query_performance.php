<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reapplies', function (Blueprint $table) {
            $table->index([DB::raw('created_at DESC')], 'reapplies_idx_created_at');
        });

        Schema::table('reapplies', function (Blueprint $table) {
            $table->index(['deleted_at', DB::raw('created_at DESC')], 'reapplies_idx_deleted_at_created_at');
        });

        Schema::table('reapplies', function (Blueprint $table) {
            $table->index(['customer_id', 'deleted_at'], 'reapplies_idx_customer_id_deleted_at');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index(['email','ssn_short','deleted_at','id'], 'customers_idx_email_ssn_sho_deleted_id');
        });

        Schema::table('customers_to_banks', function (Blueprint $table) {
            $table->index(['customer_id', 'deleted_at'], 'customers_to_banks_idx_customer_id_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reapplies', function (Blueprint $table) {
            $table->dropIndex('reapplies_idx_created_at');
        });

        Schema::table('reapplies', function (Blueprint $table) {
            $table->dropIndex('reapplies_idx_deleted_at_created_at');
        });

        Schema::table('reapplies', function (Blueprint $table) {
            $table->dropIndex('reapplies_idx_customer_id_deleted_at');
        });

        Schema::table('customers_to_banks', function (Blueprint $table) {
            $table->dropIndex('customers_to_banks_idx_customer_id_deleted_at');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_idx_email_ssn_sho_deleted_id');
        });

    }
};
