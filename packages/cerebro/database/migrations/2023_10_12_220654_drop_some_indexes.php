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
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex('click_idx');
            $table->dropIndex('ip_address_idx');
            $table->dropIndex('visitors_idx_clicks_amount');
            $table->dropIndex('visitors_idx_visits_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->index('click_id', 'click_idx');
            $table->index('ip_address', 'ip_address_idx');
            $table->index('clicks_amount', 'visitors_idx_clicks_amount');
            $table->index('visits_amount', 'visitors_idx_visits_amount');
        });
    }
};
