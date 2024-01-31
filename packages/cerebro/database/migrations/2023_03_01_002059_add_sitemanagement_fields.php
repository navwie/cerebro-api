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
        Schema::table('servers', function (Blueprint $table) {
            $table->string('base_dir')->nullable()->after('user_name');
            $table->string('lets_encrypt_email')->nullable()->after('base_dir');
            $table->string('container_name')->nullable()->after('lets_encrypt_email');
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->string('title')->nullable()->after('domain_name');
            $table->string('logo')->nullable()->after('title');
            $table->string('favicon')->nullable()->after('logo');
            $table->string('hero')->nullable()->after('favicon');
            $table->string('theme')->nullable()->after('hero');
            $table->string('main_color')->nullable()->after('theme');
            $table->string('button_color')->nullable()->after('main_color');
            $table->string('link_color')->nullable()->after('button_color');
            $table->string('radio_color')->nullable()->after('link_color');
            $table->string('radio_text_color')->nullable()->after('radio_color');
            $table->string('token')->nullable()->after('radio_text_color');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn('base_dir');
            $table->dropColumn('lets_encrypt_email');
            $table->dropColumn('container_name');
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('logo');
            $table->dropColumn('favicon');
            $table->dropColumn('hero');
            $table->dropColumn('theme');
            $table->dropColumn('main_color');
            $table->dropColumn('button_color');
            $table->dropColumn('link_color');
            $table->dropColumn('radio_color');
            $table->dropColumn('radio_text_color');
            $table->dropColumn('token');
        });
    }
};
