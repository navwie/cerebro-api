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
        Schema::table('sites', function (Blueprint $table) {
            $table->string('comfort_bg2')->nullable()->after('comfort_bg');
            $table->string('cert_rule_arn')->nullable()->after('cert_arn');
            $table->string('cert_listener_arn')->nullable()->after('cert_rule_arn');
            $table->string('cert_status')->nullable()->after('cert_listener_arn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('comfort_bg2');
            $table->dropColumn('cert_status');
            $table->dropColumn('cert_listener_arn');
            $table->dropColumn('cert_rule_arn');
        });
    }
};
