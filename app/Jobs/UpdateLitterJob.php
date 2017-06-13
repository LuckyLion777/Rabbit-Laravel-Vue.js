<?php

namespace App\Jobs;

use App\Events\EventHolderWasRenamed;
use App\Events\LitterKitsAmountWasChanged;
use App\Events\LitterParentWasChanged;
use App\Jobs\Job;
use App\Models\Litter;
use App\Repositories\LitterRepository;
use Carbon\Carbon;
use Collective\Bus\Dispatcher;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateLitterJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;
    /**
     * @var Litter
     */
    public $litter;
    /**
     * @var
     */
    public $given_id;
    /**
     * @var
     */
    public $kits_amount;
    /**
     * @var
     */
    public $father_id;
    /**
     * @var
     */
    public $mother_id;
    /**
     * @var
     */
    public $bred;
    /**
     * @var
     */
    public $born;
    /**
     * @var
     */
    public $notes;
    /**
     * @var
     */
    private $parents;
    /**
     * @var
     */
    public $butchered;

    public $butchered_date;

    /**
     * Create a new job instance.
     *
     * @param $litter
     * @param $given_id
     * @param $father_id
     * @param $mother_id
     * @param $parents
     * @param $bred
     * @param $born
     * @param $notes
     */
    public function __construct($litter, $given_id, $father_id, $mother_id, $kits_amount, $parents, $bred, $born, $notes, $butchered, $butchered_date = null)
    {
        $this->litter    = $litter;
        $this->given_id  = $given_id;
        $this->father_id = $father_id;
        $this->mother_id = $mother_id;
        $this->bred      = $bred;
        $this->born      = $born;
        $this->notes     = $notes;
        $this->parents   = $parents;
        $this->kits_amount   = $kits_amount;
        $this->butchered_date   = $butchered_date;
        $this->butchered   = $butchered;
    }

    /**
     * Execute the job.
     *
     * @param LitterRepository $litters
     */
    public function handle(LitterRepository $litters, Dispatcher $dispatcher)
    {

        $oldKitAmount = $this->litter->kits_amount;

        if ($this->given_id != $this->litter->given_id) {
            event(new EventHolderWasRenamed($this->litter, $this->given_id));
        }

        $this->litter->recordModification(function() use ($litters, $oldKitAmount, $dispatcher) {
            $litter = $litters->updateFromJob($this->litter, $this);
            if($oldKitAmount != $this->litter->kits_amount){
                $this->litter->rabbitKits()->delete();  // TODO: find better place
                $this->litter->updateWeights();
                $oldKitsDied = $this->litter->kits_died;
                $this->litter->kits_died = null;
                $this->litter->survival_rate = 100;
                $this->litter->update();
                $type = 'rabbitkit';
                $dispatcher->dispatchFromArray(CreateKitsJob::class, ['litter' => $litter, 'animal_type' => $type]);
                event(new LitterKitsAmountWasChanged($this->litter, $oldKitsDied, $oldKitAmount, $this->litter->kits_amount, $type));
            }

            if($this->butchered && !$this->litter->butchered){
                $this->litter->butchered = $this->butchered;
                $this->litter->archived= 1;
            }
            if(!$this->butchered && $this->litter->butchered){
                $this->litter->butchered = $this->butchered;
                $this->litter->butchered_at = null;
                $this->litter->archived = 0;
            }

            if($this->butchered_date){
                $this->litter->butchered_at = Carbon::createFromFormat(
                    \Auth::user()->getDateFormatPHP(),
                    $this->butchered_date
                )->toDateString();
                $this->litter->update();
            }


            $parents     = collect($this->parents);
            $oldMotherId = $parents->where('sex', 'doe')->pluck('id')->first();
            $oldFatherId = $parents->where('sex', 'buck')->pluck('id')->first();

            if ($this->father_id != $oldFatherId) {
                if($oldFatherId)
                    $this->litter->parents()->detach($oldFatherId);
                if($this->father_id)
                    $this->litter->parents()->attach($this->father_id);
                event(new LitterParentWasChanged($litter, $oldFatherId, $this->father_id,'rabbit'));
            }
            if ($this->mother_id != $oldMotherId){
                if($oldMotherId)
                    $this->litter->parents()->detach($oldMotherId);
                if($this->mother_id)
                    $this->litter->parents()->attach($this->mother_id);
                event(new LitterParentWasChanged($litter, $oldMotherId, $this->mother_id,'rabbit'));
            }



            $litter->load('parents');
            $litter->weighs    = $litter->weighs()->count();
            if($mother = $litter->parents()->where('sex', '=', 'doe')->first()){
                $litter->mother_id = $mother->id;
            }
            if($father = $litter->parents()->where('sex', '=', 'buck')->first()){
                $litter->father_id = $father->id;
            }
        });

        return $this->litter;
    }
}
