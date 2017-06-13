<?php

namespace App\Listeners;

use App\Events\KitWasArchived;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ManageKitArchive
{

    public function __construct()
    {
        //
    }


    public function updateLitterStats(KitWasArchived $event)
    {
        $litter = $event->litter;
        //$litter->kits_amount -= 1;
        $litter->survival_rate = ($litter->kits_amount - $litter->kits_died) / $litter->kits_amount * 100;
        $litter->updateWeights();
        if ( !$event->litter->kitsForButchCount() && $event->litter->kitsButchered()->count()) {
            $event->litter->archived = 1;
            $event->litter->butchered = 1;
            $event->litter->butchered_at = Carbon::now();

        }
        $litter->update();


    }

    public function updateBreederStats(KitWasArchived $event)
    {
        foreach ($event->litter->parents as $parent) {
            if($event->kit->archived == 1){
                $parent->live_kits -= 1;
            } else {
                $parent->live_kits += 1;
            }
            $parent->update();
        }
    }
}
