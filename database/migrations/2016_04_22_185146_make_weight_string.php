<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeWeightString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedigrees', function(Blueprint $table) {
            $table->string('weight', 10)->nullable()->change();
        });
        Schema::table('rabbit_breeders', function(Blueprint $table) {
            $table->string('weight', 10)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedigrees', function(Blueprint $table) {
            $table->float('weight')->nullable()->change();
        });
        Schema::table('rabbit_breeders', function(Blueprint $table) {
            $table->float('weight')->nullable()->change();
        });
    }
}
