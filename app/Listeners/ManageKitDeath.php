<?php

namespace App\Listeners;

use App\Events\KitWasDied;
use Carbon\Carbon;

class ManageKitDeath
{

    public function __construct()
    {
        //
    }


    public function updateLitterStats(KitWasDied $event)
    {
        $litter = $event->litter;
        $litter->kits_died += 1;
        $litter->survival_rate = ($litter->kits_amount - $litter->kits_died) / $litter->kits_amount * 100;
        $litter->updateWeights();
        if ( !$event->litter->kitsForButchCount() && $event->litter->kitsButchered()->count()) {
            $event->litter->archived = 1;
            $event->litter->butchered = 1;
            $event->litter->butchered_at = Carbon::now();
        }
        $litter->update();


    }

    public function updateBreederStats(KitWasDied $event)
    {
        foreach ($event->litter->parents as $parent) {
            $parent->live_kits -= 1;
            $parent->update();
        }
    }
}
