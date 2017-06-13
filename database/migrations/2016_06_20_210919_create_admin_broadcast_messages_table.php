<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminBroadcastMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broadcast', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['success', 'warning', 'info', 'danger']);
            $table->string('title');
            $table->text('content');
            $table->timestamp('disabled_at')->nullable()->default(null);

            $table->nullableTimestamps();
        });
        
        Schema::create('broadcast_dismissed', function (Blueprint $table) {
            $table->unsignedInteger('broadcast_id');
            $table->unsignedInteger('user_id');

            $table->foreign('broadcast_id')->references('id')->on('broadcast');
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
        Schema::drop('broadcast_dismissed');
        Schema::drop('broadcast');
    }
}
