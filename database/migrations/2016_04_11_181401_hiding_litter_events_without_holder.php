<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HidingLitterEventsWithoutHolder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $eventTable = (new \App\Models\Event())->getTable();
        DB::table($eventTable)->where('type', 'litter')->where('holderName', null)->update(array('archived' => 1));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $eventTable = (new \App\Models\Event())->getTable();
        DB::table($eventTable)->where('type', 'litter')->where('holderName', null)->update(array('archived' => 0));
    }
}
