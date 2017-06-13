<?php

namespace App\Listeners;

use App\Events\KitWasWeighed;
use App\Events\LitterWasWeighed;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ManageLitterWeights
{
    /**
     * @var Event
     */
    private $event;

    /**
     * Create the event listener.
     *
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Handle the event.
     *
     * @param  KitWasWeighed $event
     * @return void
     */
    public function byKitWeight(KitWasWeighed $event)
    {
        $litter = $event->kit->litter;
        $litter->updateWeights();
        $litter->update();
    }

    public function byLitterWeight(LitterWasWeighed $event)
    {
        $event->litter->updateWeights();
        $event->litter->update();

        $weighs = $event->litter->weighs()->count();
        $plannedEvent = null;
        if($weighs = $event->litter->weighs()->count() == 0){    //temporary fix for issue #127 - wrong subtype on first weigh task
            $plannedEvent = $event->litter->events()
                                ->whereNotNull('breed_id')
                                ->where('icon', 'fa-balance-scale bg-yellow first-weight')
                                ->first();
        }
        if(!$plannedEvent)
            $plannedEvent = $event->litter->events()->whereNotNull('breed_id')->where('subtype', 'weigh')->orderBy('date')->first();

        if ($plannedEvent) {
            $plannedEvent->date   = $event->date;
            $plannedEvent->closed = 1;
            $plannedEvent->update();
        } else {
            $this->event->type    = 'litter';
            $this->event->name    = 'weigh' . ($weighs + 1);
            $this->event->subtype = 'weigh';
            $this->event->closed  = 1;
            $this->event->recurring  = 1;
            $this->event->holderName  = $event->litter->given_id;
            $this->event->date    = $event->date;
            $this->event->save();
            $event->litter->events()->attach($this->event);
            auth()->user()->events()->attach($this->event);
        }
    }
}
