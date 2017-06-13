<?php

namespace App\Listeners;

use App\Events\KitWasRevived;

class ManageKitRevival
{

    public function __construct()
    {
        //
    }


    public function updateLitterStats(KitWasRevived $event)
    {
        $litter = $event->litter;
        $litter->kits_died -= 1;
        $litter->survival_rate = ($litter->kits_amount - $litter->kits_died) / $litter->kits_amount * 100;
        $litter->updateWeights();

        $litter->update();


    }

    public function updateBreederStats(KitWasRevived $event)
    {
        foreach ($event->litter->parents as $parent) {
            $parent->live_kits += 1;
            $parent->update();
        }
    }
}
