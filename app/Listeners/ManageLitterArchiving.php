<?php

namespace App\Listeners;

use App\Events\LitterWasArchived;
use App\Events\LitterWasDeleted;
use App\Events\LitterWasUnarchived;
use App\Models\BreedPlan;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ManageLitterArchiving
{

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LitterWasArchived  $event
     * @return void
     */
    public function archivePlan(LitterWasArchived $event)
    {
        $event->litter->archived_at = Carbon::now();
        $event->litter->update();
        foreach($event->litter->events as $event){
            $event->archived = 1;
            $event->update();
        }

    }

    public function unarchivePlan(LitterWasUnarchived $event)
    {
        /* @var BreedPlan $plan */
        $event->litter->archived_at = null;
        $event->litter->update();
        foreach($event->litter->rawEvents as $event){
            $event->archived = 0;
            $event->update();
        }
    }
}
