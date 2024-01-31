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
        Schema::create('clicks_to_card_site_items', function (Blueprint $table) {
            $table->id();
            $table->integer('visitor_card_id');
            $table->integer('card_site_item_id');
            $table->integer('customer_credit_card_id');
            $table->integer('click_amount');
            $table->date('date');
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
        Schema::dropIfExists('clicks_to_card_site_items');
    }
};
