<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDobToRabbitBreedersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rabbit_breeders', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('aquired');
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
            $table->dropColumn('date_of_birth');
        });
    }
}
