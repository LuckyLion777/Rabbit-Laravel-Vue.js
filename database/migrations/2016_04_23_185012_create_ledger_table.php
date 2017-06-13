<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLedgerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ledger_entries', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false);
            $table->date('date')->nullable(false);
            $table->integer('category_id')->unsigned()->nullable(false);
            $table->foreign('category_id')
                  ->references('id')->on('ledger_categories')
                  ->onDelete('restrict');
            $table->integer('user_id')->unsigned()->nullable(false);
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->boolean('debit')->default(false)->nullable(false);
            $table->decimal('amount', 8, 2)->nullable(false);
            $table->text('description')->nullable();
            $table->integer('associated_id')->unsigned()->nullable();
            $table->string('associated_type')->string()->nullable();
            
            $table->timestamp('archived_at')->nullable();
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
        Schema::drop('ledger_entries');
    }
}
