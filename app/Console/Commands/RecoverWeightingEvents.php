<?php

namespace App\Console\Commands;

use App\Events\EventWasAdded;
use App\Jobs\CreateEventJob;
use App\Models\BreedPlan;
use Carbon\Carbon;
use Collective\Bus\Dispatcher;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class RecoverWeightingEvents extends Command
{
    protected $signature = 'recover-weighting-tasks';

    public function handle(Dispatcher $dispatcher)
    {
        $purgeDate = Carbon::createFromFormat('Y-m-d', '2017-01-16');

        $plans = BreedPlan::query();
        /* @var $plans Builder|\Illuminate\Database\Query\Builder */
        $plans = $plans->whereDate('created_at', '<', $purgeDate->toDateString())
            ->whereDate('created_at', '>=', $purgeDate->copy()->subMonths(6)->toDateString())
            ->whereHas('events', function (Builder $events) use ($purgeDate) {
                return $events->where('subtype', 'birth')->whereNull('missed')
                    ->where(function (Builder $events) use ($purgeDate) {
                        // Either the event is still not closed
                        // or it was closed after the purge date
                        return $events->where('closed', 0)
                            ->orWhereDate('updated_at', '>=', $purgeDate->toDateString());
                    });
            })
            ->whereDoesntHave('events', function (Builder $events) {
                return $events->where('type', 'litter');
            })->get()->load('events', function (Builder $events) {
                return $events->where('subtype', 'birth');
            })->load('user');

        $count = $plans->count();
        $this->line("Found {$count} plans to recover");

        foreach ($plans as $plan) {
            /* @var $plan BreedPlan */
            $events = [];

            $isArchived = !$plan->events->first()->closed;

            foreach ($plan->generateEvents($plan->date) as $event) {
                if ($event['type'] !== 'litter') {
                    continue;
                }

                $data = [
                    'type' => $event['type'],
                    'type_id' => null,
                    'archived' => $isArchived,
                    'name' => $event['name'],
                    'date' => $event['date'],
                    'recurring' => 1,
                    'icon' => $event['icon'],
                ];

                $event = $dispatcher->dispatchFromArray(CreateEventJob::class, $data);
                $plan->user->events()->attach($event);

                event(new EventWasAdded($event, $plan->user));

                $events[] = $event;
            }

            $plan->events()->saveMany($events);

            --$count;
            $this->line("{$count} more to go");
        }

        $this->line("Done");
    }
}
