<?php

namespace App\Http\Controllers\Admin;

use App\Events\EventHasGone;
use App\Http\Requests\CreateEventRequest;

use App\Http\Requests\GetEventsRequest;
use App\Http\Requests\MakeBreedPlanRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Requests\UpdateEventListRequest;
use App\Jobs\CreateBreedPlanJob;
use App\Jobs\CreateEventJob;
use App\Jobs\GetEventsJob;
use App\Jobs\UpdateEventJob;
use App\Models\BreedPlan;
use App\Models\Device;
use App\Models\Event;
use App\Models\Litter;
use App\Models\RabbitBreeder;
use App\Models\User;
use App\Repositories\EventRepository;
use Carbon\Carbon;
use Collective\Bus\Dispatcher;
use Illuminate\Http\Request;
use App\Jobs\UpdateEventWithoutAuthJob;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;
class AdminEventsController extends Controller
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * AdminEventsController constructor.
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function index(GetEventsRequest $request)
    {
        $objects = $this->dispatcher->dispatchFrom(GetEventsJob::class, $request, ['pureRequest' => $request]);

        return response()->json($objects);
    }


    public function store(CreateEventRequest $request)
    {
        return $this->dispatcher->dispatchFrom(CreateEventJob::class, $request);
    }

    public function update(Event $event, UpdateEventRequest $request)
    {
        $request['event'] = $event;

        return $this->dispatcher->dispatchFrom(UpdateEventJob::class, $request);
    }

    public function getList(GetEventsRequest $request)
    {
        $request->get('device_id');

        //login trough device_id
        $device = Device::whereDeviceId($request->get('device_id'))->first();
        if (!$device) {
            return response()->json('false');
        }
        \Auth::login($device->user);

        $objects = $this->dispatcher->dispatchFrom(GetEventsJob::class, $request, ['pureRequest' => $request]);

        return response()->json($objects);
    }

    public function updateList(UpdateEventListRequest $request)
    {
        $rules = [
            'name' => 'required',
            'icon' => 'required',
            'date' => 'required',
        ];

        //login trough device_id
        $device = Device::whereDeviceId($request->get('device_id'))->first();
        if (!$device) {
            return response()->json('false');
        }
        \Auth::login($device->user);

        //save each event
        foreach($request->get('data') as $item) {

            if ($item['type'] != 'general') {
                $rules['type_id'] = 'required';
            }

            $validator = \Validator::make($item, $rules);
            if ($validator->fails()) {
                continue;
            }

            $event =  Event::find($item['id']);

            $oldDate = Carbon::parse($event->updated_at)->toDateTimeString();
            $newDate = Carbon::parse($item['updated_at'])->toDateTimeString();

            //update only changed items
            if ($newDate != $oldDate) {
                $item['event'] = $event;
                $this->dispatcher->dispatchFromArray(UpdateEventJob::class, $item);
            }
        }

    }

    public function show(Event $event)
    {
        $event->load($event->type . 's');

        return response()->json($event);
    }

    private function destroyOrArchive(Event $event, $delete = false)
    {
        if ($event->isRecurring()) {
            $event->recurringEvents()->delete();
        }

        if ($delete) {
            $event->delete();
        } else {
            $event->archive();
        }

        event(new EventHasGone($event, auth()->user()));
    }

    public function destroy(Event $event)
    {
        $this->destroyOrArchive($event, true);
    }

    public function archiveEvents(Request $request, EventRepository $events) {
        //$eventForDelete = $request->get('events');
        $listOfEvents = auth()->user()->entireEvents()->where('closed', 1)->get();
        foreach ($listOfEvents as $event) {
            if (!$event->isRecurring()) {
                $this->destroyOrArchive($event, false);
            }
        }
    }

    public function close(Event $event, EventRepository $events)
    {
        event(new EventHasGone($event, auth()->user()));
        if ( !$event->isRecurring()) {
            $event->closed = 1;
        } else {
            $event->closeRecurring();
            $events->addNewRecurringFor($event);

            $dateFormat = \Auth::user()->getDateFormatPHP();
            $date = Carbon::createFromFormat($dateFormat, $event->date);
            $event->date = $event->recurringDate($date)->format($dateFormat);

            $event->update();
        }
        $event->update();
    }

    public function reOpen(Event $event)
    {
        if ( !$event->isRecurring()) {
            $event->closed = 0;
        } else {
            $recurring         = $event->recurringOldest;
            $recurring->closed = 0;
            $recurring->update();
            $event->date = $recurring->date;
        }
        $event->update();
    }

    public function unarchive(Event $event, EventRepository $events)
    {
        $event->archived = 0;
        if ($event->isRecurring()) {
            if ($event->recurringEvents->isEmpty()) {
                $recurrings = $event->formRecurring();
                $events->addRecurring($recurrings);
            }
        }
        $event->update();
    }

    public function test(EventRepository $events)
    {
        $litter = Litter::find(2);
        $plannedEvent = $litter->weighs()->count();
        dd($plannedEvent);
    }

    public function makeBreedPlan(MakeBreedPlanRequest $request)
    {
        $this->dispatcher->dispatchFrom(CreateBreedPlanJob::class, $request);
    }

    public function breedPlanDummyEvents(Request $request, BreedPlan $breedPlan)
    {
        return $breedPlan->generateEvents($request->get('date'));
    }

}
