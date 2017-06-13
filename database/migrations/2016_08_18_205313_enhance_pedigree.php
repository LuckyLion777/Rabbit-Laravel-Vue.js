<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EnhancePedigree extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('pedigrees', function (Blueprint $table) {
            $table->string('legs')->nullable(true)->default(null);
            $table->string('registration_number')->nullable(true)->default(null);
            $table->string('champion_number')->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('pedigrees', function (Blueprint $table) {
            $table->dropColumn(['legs', 'registration_number', 'champion_number']);
        });
    }
}
