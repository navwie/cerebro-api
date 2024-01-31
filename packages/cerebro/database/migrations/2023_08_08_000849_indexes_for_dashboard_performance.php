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
            $table->index('visits_amount', 'visitors_idx_visits_amount');
        });

        Schema::table('visitors', function (Blueprint $table) {
            $table->index('clicks_amount', 'visitors_idx_clicks_amount');
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->index(['lead_type'], 'reapplies_idx_lead_type');
        });

        Schema::connection('mysql_audit')->table('decisions', function (Blueprint $table) {
            $table->index(['decision_status'], 'reapply_idx_decision_status');
        });

        Schema::connection('mysql_audit')->table('decisions', function (Blueprint $table) {
            $table->index(['redirected'], 'reapply_idx_redirected');
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
            $table->dropIndex('visitors_idx_visits_amount');
        });

        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex('visitors_idx_clicks_amount');
        });

        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->dropIndex('reapplies_idx_lead_type');
        });

        Schema::connection('mysql_audit')->table('decisions', function (Blueprint $table) {
            $table->dropIndex('reapply_idx_decision_status');
        });

        Schema::connection('mysql_audit')->table('decisions', function (Blueprint $table) {
            $table->dropIndex('reapply_idx_redirected');
        });
    }
};
