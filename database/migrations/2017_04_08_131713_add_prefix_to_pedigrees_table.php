<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrefixToPedigreesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedigrees', function (Blueprint $table) {
            $table->string('prefix',255)->nullable()->after('level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedigrees', function (Blueprint $table) {
            $table->dropColumn('prefix');
        });
    }
}
