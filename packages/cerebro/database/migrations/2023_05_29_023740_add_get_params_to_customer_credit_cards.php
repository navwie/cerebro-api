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
        Schema::table('customer_credit_cards', function (Blueprint $table) {
            $table->string('click_id')->nullable()->after('state');
            $table->json('sub_ids')->nullable()->after('click_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_credit_cards', function (Blueprint $table) {
            $table->dropColumn('click_id');
            $table->dropColumn('sub_ids');
        });
    }
};
