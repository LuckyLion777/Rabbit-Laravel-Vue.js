<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChroniclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chronicles', function (Blueprint $table) {
            $table->increments('id');

            $table->string('subject_type')->nullable(false);
            $table->unsignedInteger('subject_id')->nullable(false);
            $table->string('event')->nullable(false);

            // You can replace this with json/jsonb if your driver supports it
            $table->text('data')->nullable(true)->default(null);

            // Sometimes system user modifies entity (or anonymous), so let this field be mullable
            $table->unsignedInteger('user_id')->nullable(true)->default(null);

            $table->timestamp('ts')->nullable(false);

            $table->index(['subject_type', 'subject_id', 'event', 'ts']);

            // Correct this to point to your users table and pk
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chronicles');
    }
}
