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
        Schema::table('master_stations', function (Blueprint $table) {
            $table->unsignedBigInteger('last_timestamp')->after('name');
            $table->boolean('is_active')->nullable()->default(false)->after('last_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_stations', function (Blueprint $table) {
            //
        });
    }
};
