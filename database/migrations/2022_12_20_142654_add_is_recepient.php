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
        Schema::table('master_email_sends', function (Blueprint $table) {
            $table->boolean("is_to")->default(0)->after("name");
            $table->boolean("is_cc")->default(0)->after('is_to');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_email_sends', function (Blueprint $table) {
            //
        });
    }
};
