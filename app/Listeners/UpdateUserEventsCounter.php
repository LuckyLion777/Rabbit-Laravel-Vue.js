<?php

namespace App\Listeners;

use App\Events\EventDateWasChanged;
use App\Events\EventHasGone;
use App\Events\EventWasAdded;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateUserEventsCounter
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EventWasAdded $event
     * @return void
     */
    public function increase(EventWasAdded $event)
    {
        if ($event->event->dateLessThan(Carbon::now()->addWeek())) {
            $event->user->events += 1;
            $event->user->update();
        }

    }

    public function manage(EventDateWasChanged $event)
    {
        $userDateFormat = $event->user->getDateFormatPHP();
        $oldInWeek = dateLessThan($event->oldDate, Carbon::now()->addWeek(), $userDateFormat);
        $newInWeek = dateLessThan($event->newDate, Carbon::now()->addWeek(), $userDateFormat);
        if ($oldInWeek && !$newInWeek) {
            $event->user->events -= 1;
            $event->user->update();
        } elseif ( !$oldInWeek && $newInWeek) {
            $event->user->events += 1;
            $event->user->update();

        }
    }

    public function decrease(EventHasGone $event)
    {
        if ($event->event->dateLessThan(Carbon::now()->addWeek(), $event->user->getDateFormatPHP())
                && $event->event->closed == 0) {
            $event->user->events -= 1;
            $event->user->update();
        }
    }
}
