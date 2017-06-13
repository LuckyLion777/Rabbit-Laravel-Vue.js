<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeathReasonToBreeders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('rabbit_breeders', function (Blueprint $table) {
            $table->string('death_reason')->nullable(true)->default(null);
        });

        \Schema::table('rabbit_kits', function (Blueprint $table) {
            $table->string('death_reason')->nullable(true)->default(null)->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('rabbit_breeders', function (Blueprint $table) {
            $table->dropColumn(['death_reason']);
        });

        \Schema::table('rabbit_kits', function (Blueprint $table) {
            $table->dropColumn(['death_reason']);
        });
    }
}
