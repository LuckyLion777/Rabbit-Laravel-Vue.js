<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArchivedAtDateToLitterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('litters', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('archived');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('litters', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });
    }
}
