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
            $table->string('footer_button_color')->nullable()->after('footer_background_color');
            $table->string('footer_text_color')->nullable()->after('footer_button_color');
            $table->string('comfort_button_color')->nullable()->after('footer_text_color');
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
            $table->dropColumn('footer_button_color');
            $table->dropColumn('footer_text_color');
            $table->dropColumn('comfort_button_color');
        });
    }
};
