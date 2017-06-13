<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserReferredByAndSlugFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug', 4)->nullable()->default(null)->unique();
            $table->unsignedInteger('referred_by_id')->nullable(true)->default(null);
            $table->foreign('referred_by_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by_id']);
            $table->dropColumn(['slug', 'referred_by_id']);
        });
    }
}
