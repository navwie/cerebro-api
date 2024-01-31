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
            $table->string('why_us_bg')->nullable()->after('hero');
            $table->string('advantages_bg')->nullable()->after('why_us_bg');
            $table->string('comfort_bg')->nullable()->after('advantages_bg');
            $table->string('last_bg')->nullable()->after('comfort_bg');
            $table->string('footer_logo')->nullable()->after('logo');

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
            $table->dropColumn('why_us_bg');
            $table->dropColumn('advantages_bg');
            $table->dropColumn('comfort_bg');
            $table->dropColumn('last_bg');
            $table->dropColumn('footer_logo');
        });
    }
};
