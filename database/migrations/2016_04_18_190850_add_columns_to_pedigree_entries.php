<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToPedigreeEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedigrees', function (Blueprint $table) {
            $table->date('aquired')->nullable()->after('day_of_birth');
            $table->string('color')->nullable()->after('aquired');
            $table->float('weight')->nullable()->after('color');
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
            $table->dropColumn('aquired');
            $table->dropColumn('color');
            $table->dropColumn('weight');
        });
    }
}
