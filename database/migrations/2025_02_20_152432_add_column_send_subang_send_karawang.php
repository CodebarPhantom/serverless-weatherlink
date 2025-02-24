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
            $table->boolean('send_subang')->default(0)->after('is_active');
            $table->boolean('send_karawang')->default(0)->after('send_subang');
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
