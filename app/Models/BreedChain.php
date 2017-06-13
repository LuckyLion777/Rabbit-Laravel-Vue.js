<?php

namespace App\Models;

use App\Jobs\CreateEventJob;
use Carbon\Carbon;
use Collective\Bus\Dispatcher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read User $user
 */
class BreedChain extends Model
{

    protected $casts = [
        'days' => 'int',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addToExistingPlans($type)
    {
        $userDateFormat = \Auth::user()->getDateFormatPHP();
        $plans = $this->user->plans()->where(function (Builder $plans) {
            return $plans->whereDoesntHave('litter')->orwhereHas('litter', function (Builder $litter) {
                return $litter->archived(0)->butchered(0);
            });
        })->where('missed', 0)->get();

        foreach ($plans as $plan) {
            /* @var BreedPlan $plan */

            $date = Carbon::createFromFormat($userDateFormat, $plan->date)->addDays($this->days);
            if (!$date->isFuture()) {
                continue;
            }

            $birthed = ($event = $plan->events->first(function ($_, Event $event) {
                return $event->subtype === 'birth';
            })) && $event->closed;

            $data = compact('type') + [
                'name' => $this->name,
                'date'  =>  $date->format($userDateFormat),
                'icon' => $this->icon,
                'recurring' => 1,
            ];
            if ($type == 'breeder') {
                $data['type_id'] = ($doe = $plan->breeders()->where('sex', 'doe')->first()) ? $doe->id : null;
            } else {
                $data['type_id'] = null;
            }
            $data['archived'] = $type == 'litter' && !$birthed ? 1 : 0;

            $newEvent = app(Dispatcher::class)->dispatchFromArray(CreateEventJob::class, $data);
            /* @var Event $newEvent */

            if ($this->icon == 'fa-venus-mars bg-blue'
                    && ($buck = $plan->breeders()->where('sex', 'buck')->first())) {
                $newEvent->breeders()->attach($buck);
                $newEvent->holderName .= ' & ' . $buck->name;
            }

            $plan->events()->save($newEvent);
        }
    }
}
