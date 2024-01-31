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
            $table->string('hero2')->nullable()->after('hero');
            $table->string('header_color')->nullable()->after('hero2');
            $table->string('header_bg_color')->nullable()->after('header_color');

            $table->string('comfort_bg3')->nullable()->after('comfort_bg2');
            $table->string('comfort_bg4')->nullable()->after('comfort_bg3');

            $table->string('why_us_bg2')->nullable()->after('why_us_bg');
            $table->string('why_us_icon_fast')->nullable()->after('why_us_bg2');
            $table->string('why_us_icon_easy')->nullable()->after('why_us_icon_fast');
            $table->string('why_us_icon_secure')->nullable()->after('why_us_icon_easy');
            $table->string('why_us_bg_color')->nullable()->after('why_us_icon_secure');
            $table->string('why_us_text_color')->nullable()->after('why_us_bg_color');
            $table->string('why_us_cards_text_color')->nullable()->after('why_us_text_color');
            $table->string('why_us_cards_bg_color')->nullable()->after('why_us_cards_text_color');
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
            $table->dropColumn('hero2');
            $table->dropColumn('header_color');
            $table->dropColumn('header_bg_color');

            $table->dropColumn('comfort_bg3');
            $table->dropColumn('comfort_bg4');

            $table->dropColumn('why_us_bg2');
            $table->dropColumn('why_us_icon_fast');
            $table->dropColumn('why_us_icon_easy');
            $table->dropColumn('why_us_icon_secure');
            $table->dropColumn('why_us_bg_color');
            $table->dropColumn('why_us_text_color');
            $table->dropColumn('why_us_cards_text_color');
            $table->dropColumn('why_us_cards_bg_color');
        });
    }
};
