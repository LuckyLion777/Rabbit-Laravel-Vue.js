<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDefaultDigestDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('digest_day')->default(-1)->change();
        });

        DB::table('users')->where('digest_day', '=', 0)->update(array('digest_day' => -1));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('digest_day')->default(0)->change();
        });

        DB::table('users')->where('digest_day', '=', -1)->update(array('digest_day' => 0));
    }
}
