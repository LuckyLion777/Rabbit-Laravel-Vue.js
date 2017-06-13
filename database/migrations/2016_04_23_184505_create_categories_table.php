<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ledger_categories', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false);
            $table->text('description')->nullable();
            $table->string('special')->nullable()->comment('Only for special categories, which are locked and visible to all of the users');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->nullableTimestamps();
        });

        foreach (['general', 'breeder', 'litter'] as $special) {
            DB::table('ledger_categories')->insert([[
                'name' => ucfirst($special),
                'special' => $special,
                'created_at' => DB::raw('CURRENT_TIMESTAMP'),
                'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
            ]]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ledger_categories');
    }
}
