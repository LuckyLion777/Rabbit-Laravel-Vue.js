<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeSocialAccountUserIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('social_accounts')->whereNull('user_id')->delete();
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
}
