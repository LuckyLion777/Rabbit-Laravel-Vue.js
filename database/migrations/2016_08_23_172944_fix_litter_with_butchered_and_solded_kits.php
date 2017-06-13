<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixLitterWithButcheredAndSoldedKits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $litters = \App\Models\Litter::where('butchered', 0)->where('archived', 0)->has('kitsForButch', '=', 0)->has('kitsButchered', '>', 0)->get();

        foreach ($litters as $litter) {
            echo $litter->id . "\n";
            $litter->butchered = 1;
            $litter->butchered_at = null;
            $litter->archived = 1;
            $litter->save();
        }

        echo $litters->count();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
