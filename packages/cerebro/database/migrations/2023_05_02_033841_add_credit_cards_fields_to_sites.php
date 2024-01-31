<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->enum('site_type', ['loan', 'card'])->after('domain_name')->default('loan');
            $table->string('header')->nullable()->after('title');
            $table->string('sub_header')->nullable()->after('header');
            $table->string('card_button_text')->nullable()->after('sub_header');
        });

        Schema::create('card_site_items', function (Blueprint $table){
            $table->id();
            $table->bigInteger('site_id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description');
            $table->string('btn_color_first');
            $table->string('btn_color_second');
            $table->string('btn_text');
            $table->string('btn_url');
            $table->string('stars');
            $table->json('benefits');
            $table->timestamps();
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
            $table->dropColumn('site_type');
            $table->dropColumn('header');
            $table->dropColumn('sub_header');
            $table->dropColumn('card_button_text');
        });

        Schema::drop('card_site_items');
    }
};
