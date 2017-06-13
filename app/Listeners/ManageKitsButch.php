<?php

namespace App\Listeners;

use App\Events\KitsWereButched;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ManageKitsButch
{

    /**
     * @var Event
     */
    private $butchEvent;

    public function __construct(Event $butchEvent)
    {
        $this->butchEvent = $butchEvent;
    }

    public function updateLitterStats(KitsWereButched $event)
    {
        $event->litter->updateWeights();
        if ( !$event->litter->kitsForButchCount()) {
            $event->litter->archived = 1;
            $event->litter->butchered = 1;
            if($event->date){
                $event->litter->butchered_at = Carbon::createFromFormat(\Auth::user()->getDateFormatPHP(), $event->date);
            } else {
                $event->litter->butchered_at = Carbon::now();
            }
        }
        $event->litter->update();
    }

    public function updateBreederStats(KitsWereButched $event)
    {
        foreach ($event->litter->parents as $parent) {//TODO change in case of multiple animal types
            $parent->live_kits -= $event->amount;
            $parent->update();
        }
    }

    public function autoCloseBreedPlanEvent(KitsWereButched $event)
    {
        $plannedEvent = $event->litter->events()->whereNotNull('breed_id')->where('subtype', 'butch')->first();

        if ($plannedEvent) {
            $plannedEvent->date   = Carbon::now()->format(\Auth::user()->getDateFormatPHP());
            $plannedEvent->closed = 1;
            $plannedEvent->update();
        }

//        else {
//            $this->butchEvent->type    = 'litter';
//            $this->butchEvent->name    = 'butcher';
//            $this->butchEvent->subtype = 'butch';
//            $this->butchEvent->closed  = 1;
//            $this->butchEvent->date    = $event->date;
//            $this->butchEvent->save();
//            $event->litter->events()->attach($this->butchEvent);
//        }

    }
}
