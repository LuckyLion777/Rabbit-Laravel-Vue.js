<?php

namespace App\Listeners;

use App\Events\KitWasSold;
use Carbon\Carbon;

class ManageKitsSold
{

    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  KitWasSold  $event
     * @return void
     */
    public function handle(KitWasSold $event)
    {

        if ( !$event->litter->kitsForButchCount() && $event->litter->kitsButchered()->count()) {
            $event->litter->archived = 1;
            $event->litter->butchered = 1;
            $event->litter->butchered_at = Carbon::now();
            $event->litter->update();
        }

    }
}
