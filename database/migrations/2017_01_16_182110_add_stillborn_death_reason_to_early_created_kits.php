<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStillbornDeathReasonToEarlyCreatedKits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $kits = \App\Models\RabbitKit::where('death_reason', null)->where('created_at', DB::raw('updated_at'))->where('alive', 0)->get();
        foreach ($kits as $kit) {
            echo $kit->id . "\n";
            $kit->death_reason = $kit->getDefaultDeathReason();
            $kit->save();
        }
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
