<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPedigreeForKits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedigrees', function(Blueprint $table) {
            $table->integer('rabbit_kit_id')->unsigned()->nullable();
            $table->foreign('rabbit_kit_id')
                ->references('id')->on('rabbit_kits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedigree', function(Blueprint $table) {
            $table->dropForeign('rabbit_kit_id');
            $table->dropColumn('rabbit_kit_id');
        });
    }
}
