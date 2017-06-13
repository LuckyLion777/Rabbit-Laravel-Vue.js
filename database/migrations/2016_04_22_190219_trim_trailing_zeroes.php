<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TrimTrailingZeroes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('pedigrees')->whereRaw('weight IS NOT NULL')
            ->update(['weight' => DB::raw("TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM weight))")]);
        DB::table('rabbit_breeders')->whereRaw('weight IS NOT NULL')
            ->update(['weight' => DB::raw("TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM weight))")]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
