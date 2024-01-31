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
        Schema::connection('mysql_audit')->table('reapplies', function (Blueprint $table) {
            $table->boolean('denied_mark')->default(0)->after('risk_processed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_audit')->table('audit_reapplies', function (Blueprint $table) {
            $table->dropColumn('denied_mark');
        });
    }
};
