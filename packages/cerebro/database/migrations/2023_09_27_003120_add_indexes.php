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
        Schema::table('logs_reapply_search', function (Blueprint $table) {
            $table->index('user_id', 'logs_reapply_search_user_id_index');
            $table->index(['created_at','search_type'], 'logs_reapply_search_dashboard_indexes');
        });

        Schema::table('visitors', function (Blueprint $table) {
            $table->index('flow_id', 'visitors_flow_id_index');
            $table->index('referral_id', 'visitors_referral_id_index');
            $table->index('action_type', 'visitors_action_type_index');
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->index('flow_id', 'reapplies_flow_id_index');
            $table->index('action_type', 'reapplies_action_type_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs_reapply_search', function (Blueprint $table) {
            $table->dropIndex('logs_reapply_search_user_id_index');
            $table->dropIndex('logs_reapply_search_dashboard_indexes');
        });

        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex('visitors_flow_id_index');
            $table->dropIndex('visitors_referral_id_index');
            $table->dropIndex('visitors_action_type_index');
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->dropIndex('reapplies_flow_id_index');
            $table->dropIndex('reapplies_action_type_index');
        });
    }
};
