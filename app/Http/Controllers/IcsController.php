<?php

namespace App\Http\Controllers;

use App\Contracts\CryptHash;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event as CalendarEvent;

class IcsController extends Controller
{
    public function download(User $user, $hash, CryptHash $crypt)
    {
        if (!$crypt->check('user:' . $user->id . ':schedule', $hash)) {
            abort(403, 'Access denied');
        }

        $events = $user->eventsCopy->load(['recurringOpen', 'litters', 'breeders']);

        $calendar = new Calendar('-//Barntrax//NONSGML Hutch v1.0//EN');
        $calendar->setName('Hutch Schedule')->setDescription('Hutch Schedule')->setPublishedTTL('P1D');
        foreach ($events as $event) {
            /* @var $event Event */
            switch ($event->type) {
                case 'general':
                    $description = 'General task';
                    $name = $event->name;
                    break;
                case 'litter':
                    $litter = $event->litters->first();
                    $description = 'Task for litter' . ($litter ? ' ' . $litter->given_id : '');
                    $name = 'Litter' . ($litter ?  ' ' . $litter->given_id : '') . ': ' . $event->name;
                    break;
                case 'breeder':
                    $breeder = $event->breeders->first();
                    $description = 'Task for breeder' . ($breeder
                                                            ? ' #' . $breeder->tattoo . ' (' . $breeder->name . ')'
                                                            : '');
                    $name = ($breeder ? $breeder->name : 'Breeder') . ': ' . $event->name;
                    break;
            }
            switch ($event->recurring) {
                case 1:
                    $calendar->addComponent(
                        $this->makeEvent(
                            'event-' . $event->id,
                            $name,
                            $description,
                            //Carbon::createFromFormat($user->getDateFormatPHP(), $event->date)->startOfDay(),
							Carbon::parse($event->date)->startOfDay(),
                            $event->created_at
                        )
                    );
                    break;
                default:
                    foreach ($event->recurringOpen as $rec) {
                        $calendar->addComponent(
                            $this->makeEvent(
                                'recurrence-' . $rec->id,
                                $name,
                                $description,
                                //Carbon::createFromFormat($user->getDateFormatPHP(), $rec->date)->startOfDay(),
								Carbon::parse($rec->date)->startOfDay(),
                                $event->created_at
                            )
                        );
                    }
            }

        }

        return response($calendar->render(), 200, [
            'Content-Type' => 'application/ics',
            'Content-Disposition' => 'attachment; filename="htch-schedule.ics"'
        ]);
    }

    private function makeEvent($id, $summary, $description, $start, $created)
    {
        $event = new CalendarEvent('htch-' . $id);
        return $event->setSummary($summary)
                ->setDescription($description)
                ->setNoTime(true)
                ->setDtStart($start)
                ->setCreated($created)
                ->setModified(new \DateTime());
    }
}
