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

class MissedBreedPlanJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;

    private $plan;

    /**
     * Create a new job instance.
     *
     * @param $plan
     */
    public function __construct($plan)
    {
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
        $plan->missed = true;
        $plan->missed_date = Carbon::now()->format(\Auth::user()->getDateFormatPHP());
        $plan->events()->delete();
        $plan->save();
    }
}
