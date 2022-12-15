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
        Schema::table('weather_histories', function (Blueprint $table) {
            $table->double("rain_rate",6,2,true)->after("rain_rate_hi_mm")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weather_histories', function (Blueprint $table) {
            $table->dropColumn("rain_rate");
        });
    }
};
