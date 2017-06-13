<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transferable_type', 70);
            $table->unsignedInteger('transferable_id');
            $table->unsignedInteger('user_id')->nullable()->default(null);
            $table->string('user_email')->nullable()->default(null);
            $table->boolean('accepted')->default(false);
            $table->timestamp('resolved_at')->nullable()->default(null);
            
            $table->nullableTimestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('transfers');
    }
}
