<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BreederCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rabbit_breeder_categories', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false);
            $table->text('description')->nullable();
            $table->string('special')->nullable()->comment('Only for special categories, which are locked and visible to all of the users');
            $table->boolean('default')->nullable(false)->default(false);
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->nullableTimestamps();
        });

        DB::table('rabbit_breeder_categories')->insert([
            'name' => 'General',
            'special' => 'general',
            'default' => true,
            'created_at' => DB::raw('CURRENT_TIMESTAMP'),
            'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rabbit_breeder_categories');
    }
}
