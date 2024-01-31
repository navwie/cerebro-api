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
        Schema::table('visitors_card', function (Blueprint $table) {
            $table->index('referral_id', 'referral_idx');
            $table->index(['created_at', 'updated_at'], 'timestamp_idx');
            $table->index('click_id', 'click_idx');
            $table->index('ip_address', 'ip_address_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitors_card', function (Blueprint $table) {
            $table->dropIndex('referral_idx');
            $table->dropIndex('timestamp_idx');
            $table->dropIndex('click_idx');
            $table->dropIndex('ip_address_idx');
        });
    }
};
