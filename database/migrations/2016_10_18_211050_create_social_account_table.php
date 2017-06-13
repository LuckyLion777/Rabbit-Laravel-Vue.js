<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password', 60)->nullable(true)->default(null)->change();
        });

        Schema::create('social_accounts', function (Blueprint $table) {
            $table->increments('id');

            $table->string('provider');
            $table->string('provider_id');

            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->text('data');

            $table->unsignedInteger('user_id');

            $table->unique(['provider', 'provider_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('social_accounts');
        Schema::table('users', function (Blueprint $table) {
            $table->string('password', 60)->nullable(false)->change();
        });
    }
}
