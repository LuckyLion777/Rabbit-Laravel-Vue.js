<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetDefaultNumberGenerations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('pedigree_number_generations')->default(3)->change();
        });
        \App\Models\User::where('pedigree_number_generations', 0)->update(['pedigree_number_generations' => 3]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->string('pedigree_number_generations')->default(0)->change();
        });
    }
}
