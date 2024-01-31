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
            $table->dropColumn('base_dir');
            $table->dropColumn('lets_encrypt_email');
            $table->dropColumn('container_name');
            $table->dropColumn('user_name');
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->string('cert_arn')->nullable()->after('is_ssl');
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
            $table->string('user_name');
            $table->string('base_dir')->nullable()->after('user_name');
            $table->string('lets_encrypt_email')->nullable()->after('base_dir');
            $table->string('container_name')->nullable()->after('lets_encrypt_email');
        });
    }
};
