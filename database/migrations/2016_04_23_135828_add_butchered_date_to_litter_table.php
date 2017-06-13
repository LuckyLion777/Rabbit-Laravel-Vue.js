<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddButcheredDateToLitterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('litters', function (Blueprint $table) {
            $table->timestamp('butchered_at')->nullable()->after('archived');
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
            $table->dropColumn('butchered_at');
        });
    }
}
