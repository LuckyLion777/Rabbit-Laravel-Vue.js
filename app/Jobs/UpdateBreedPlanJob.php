<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\BreedPlan;
use App\Repositories\EventRepository;
use Carbon\Carbon;
use Collective\Bus\Dispatcher;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateBreedPlanJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;
    private $doe;
    private $buck;
    private $date;
    private $type;
    private $plan;

    /**
     * Create a new job instance.
     *
     * @param $doe
     * @param $buck
     * @param $date
     * @param $type
     */
    public function __construct($doe, $buck, $date, $type, $plan)
    {
        $this->doe  = $doe;
        $this->buck = $buck;
        $this->date = $date;
        $this->type = $type;
        $this->plan = $plan;
    }

    /**
     * Execute the job.
     *
     * @param BreedPlan $plan
     * @param Dispatcher $dispatcher
     */
    public function handle(Dispatcher $dispatcher)
    {
        $plan = $this->plan;
        $animals   = \App::make($this->type)->whereIn('id', [$this->doe, $this->buck])->select(['id', 'name'])->get();

        $plan->breeders()->detach();
        $plan->breeders()->attach($animals->lists('id')->toArray());

        $userDateFormat = \Auth::user()->getDateFormatPHP();
        $events = $plan->events;

        //calculate difference between planned and given
        $diffDays = Carbon::createFromFormat($userDateFormat, $plan->date)
            ->diffInDays(Carbon::createFromFormat($userDateFormat, $this->date), false);

        //update the chain of events according to date difference
        foreach ($events as $event) {
            $date = Carbon::createFromFormat($userDateFormat, $event->date);
            if ($event->type == 'breeder'){

                if($event->breeders()->count() == 2){
                    $event->holderName = $animals->where('id', (int)$this->doe)->first()->name . ' & ' . $animals->where('id', (int)$this->buck)->first()->name;
                    $event->breeders()->detach();
                    $event->breeders()->attach([$this->doe, $this->buck]);
                } else {
                    $event->holderName = $animals->where('id', (int)$this->doe)->first()->name;
                    $event->breeders()->detach();
                    $event->breeders()->attach([$this->doe]);
                }
            }
            if ($diffDays < 0) {
                $event->date = $date->subDays(abs($diffDays))->format($userDateFormat);
            } else
                $event->date = $date->addDays(abs($diffDays))->format($userDateFormat);
            $event->update();
        }
        $plan->update([
            'name' => implode(' & ', $animals->lists('name')->toArray()),
            'date' => Carbon::createFromFormat($userDateFormat, $this->date)->toDateString(),
        ]);
        $plan->save();
    }
}
