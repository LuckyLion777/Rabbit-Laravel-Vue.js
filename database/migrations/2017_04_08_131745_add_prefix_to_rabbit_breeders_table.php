<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrefixToRabbitBreedersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rabbit_breeders', function (Blueprint $table) {
            $table->string('prefix',255)->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rabbit_breeders', function (Blueprint $table) {
            $table->dropColumn('prefix');
        });
    }
}
