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
        Schema::table('customer_states', function (Blueprint $table) {
            $table->index(['email', 'click_id'], 'customer_states_idx_email_click_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_states', function (Blueprint $table) {
            $table->dropIndex('customer_states_idx_email_click_id');
        });
    }
};
