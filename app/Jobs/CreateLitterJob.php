<?php

namespace App\Jobs;

use App\Events\LitterWasArchived;
use App\Events\LitterWasCreated;
use App\Jobs\Job;
use App\Models\BreedPlan;
use App\Models\Litter;
use App\Repositories\LitterRepository;
use Carbon\Carbon;
use Collective\Bus\Dispatcher;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class CreateLitterJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;

    public  $given_id;
    public  $bred;
    public  $born;
    public  $father_id;
    public  $mother_id;
    public  $kits_amount;
    public  $dead_kits_amount;
    public  $notes;
    public  $survival_rate = 100;
    private $animal_type;
    private $breedplan;

    private $litter;

    /**
     * Create a new job instance.
     *
     * @param $given_id
     * @param $bred
     * @param $born
     * @param $father_id
     * @param $mother_id
     * @param $kits_amount
     * @param $notes
     * @param $animal_type
     */
    public function __construct($given_id, $bred, $born, $father_id, $mother_id, $kits_amount, $notes, $animal_type, $breedplan, $dead_kits_amount)
    {
        $this->given_id    = $given_id;
        $this->bred        = $bred;
        $this->born        = $born;
        $this->father_id   = $father_id;
        $this->mother_id   = $mother_id;
        $this->kits_amount = $kits_amount;
        $this->dead_kits_amount = $dead_kits_amount;
        $this->notes       = $notes;
        $this->animal_type = $animal_type;
        $this->breedplan   = $breedplan;
    }

    /**
     * Execute the job.
     *
     * @param LitterRepository $litters
     * @param Dispatcher $dispatcher
     * @param BreedPlan $breedPlan
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function handle(LitterRepository $litters, Dispatcher $dispatcher, BreedPlan $breedPlan)
    {
        return DB::transaction(function () use ($litters, $dispatcher, $breedPlan) {
            $litters->getChronicler()->recordCreation(function () use ($litters, $dispatcher, $breedPlan) {
                $this->litter = $litter = $litters->createFromJob($this);
                if(isset($this->dead_kits_amount)){
                    $litter->kits_died = $this->dead_kits_amount;
                }
                //attaching parents
                if ($this->mother_id && $this->father_id)
                    $litter->parents()->attach([$this->mother_id, $this->father_id]);//todo change in case of many type of animals

                //creating empty kits
                $dispatcher->dispatchFromArray(CreateKitsJob::class, ['litter' => $litter, 'animal_type' => $this->animal_type . 'kit']);

                if(isset($this->dead_kits_amount)){
                    $litter->kits_amount = $litter->kits_amount + $this->dead_kits_amount;
                    $litter->survival_rate = ($litter->kits_amount - $litter->kits_died) / $litter->kits_amount * 100;
                }

                $userDateFormat = \Auth::user()->getDateFormatPHP();

                //attach to existing breed plan
                if ($this->breedplan) {
                    /* @var BreedPlan $plan */
                    $plan = $breedPlan->find($this->breedplan);
                    $events = $plan->events;

                    //calculate difference between planned and given

                    $birth = $events->where('subtype', 'birth')->first();
                    if($birth) {
                        $birth->closed = 1;
                        $diffDays = Carbon::createFromFormat($userDateFormat, $birth->date)
                            ->diffInDays(Carbon::createFromFormat($userDateFormat, $this->born), false);
                        $birth->date = $this->born;
                        $birth->update();
                    } else {
                        $diffDays = 0;
                    }

                    //update the chain of events according to date difference
                    foreach ($events->where('type', 'litter') as $event) {
                        $date = Carbon::createFromFormat($userDateFormat, $event->date);
                        $event->archived = 0;
                        $event->closed = 0;
                        if ($diffDays < 0) {
                            $event->date = $date->subDays(abs($diffDays))->format($userDateFormat);
                        } else
                            $event->date = $date->addDays(abs($diffDays))->format($userDateFormat);
                        $event->update();
                    }
                    $breedEvent = $events->where('subtype', 'breed')->first();
                    if($breedEvent){
                        $litter->bred = $breedEvent->date;
                    } else {
                        $litter->bred = $plan->date;
                    }
                    $litter->parents()->attach($plan->breeders()->lists('id')->toArray());
                    $litter->update();
                    $litter->events()->attach($events->where('type', 'litter')->lists('id')->toArray());
                    $litter->rawEvents()->update(['holderName' => $litter->given_id]);
                }

                auth()->user()->litters()->save($litter);
                event(new LitterWasCreated($litter, $this->animal_type));
                if ($this->kits_amount == 0) {
                    $litter->archived = 1;
                    event(new LitterWasArchived($litter, 'rabbit'));
                }
                return $litter;
            });
            return $this->litter;
        });
    }
}
