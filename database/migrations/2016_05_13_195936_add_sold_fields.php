<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoldFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rabbit_breeders', function (Blueprint $table) {
            $table->timestamp('sold_at')->nullable()->default(null)->after('archived');
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
            $table->dropColumn('sold_at');
        });
    }
}
