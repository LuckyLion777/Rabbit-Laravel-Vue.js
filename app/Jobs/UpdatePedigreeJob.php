<?php

namespace App\Jobs;

use App\Handlers\ImageHandler;
use App\Jobs\Job;
use App\Models\RabbitBreeder;
use App\Models\Pedigree;
use App\Repositories\BreederCategoryRepository;
use App\Repositories\LitterRepository;
use App\Repositories\PedigreeRepository;
use App\Repositories\RabbitBreederRepository;
use App\Repositories\RabbitKitRepository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Handlers\CloudinaryImageHandler;

class UpdatePedigreeJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var Pedigree
     */
    public $pedigree;
    /**
     * @var
     */
    public $prefix;
    /**
     * @var
     */
    public $name;
    /**
     * @var
     */
    public $custom_id;
    /**
     * @var
     */
    public $day_of_birth;
    /**
     * @var
     */
    public $aquired;
    /**
     * @var
     */
    public $color;
    /**
     * @var
     */
    public $weight;
    /**
     * @var
     */
    public $breed;
    /**
     * @var
     */
    public $sex;
    /**
     * @var
     */
    public $legs;
    /**
     * @var
     */
    public $registration_number;
    /**
     * @var
     */
    public $champion_number;
    /**
     * @var
     */
    public $image;
    /**
     * @var
     */
    public $notes;
    /**
    *   @var
    **/
    public $rabbit;


    /**
     * Create a new job instance.
     *
     * @param $pedigree
     * @param prefix
     * @param $name
     * @param $custom_id
     * @param $day_of_birth
     * @param $breed
     * @param $sex
     * @param $image
     * @param $notes
     * @param $aquired
     * @param $weight
     * @param $color
     * @param $legs
     * @param $registration_number
     * @param $chamption_number
     * @param $rabbit
     */
    public function __construct($pedigree, $prefix, $name, $custom_id, $day_of_birth, $breed, $sex,
                                $image, $notes, $aquired, $weight, $color, $legs, $registration_number, $champion_number, $rabbit)
    {
        $this->pedigree   = $pedigree;
        $this->prefix      = $prefix;
        $this->name      = $name;
        $this->custom_id      = $custom_id;
        $this->day_of_birth    = $day_of_birth;
        $this->date_of_birth = $day_of_birth;
        $this->breed    = $breed;
        $this->sex       = $sex;
        $this->image     = $image;
        $this->notes     = $notes;
        $this->aquired = $aquired;
        $this->color = $color;
        $this->weight = $weight;
        $this->legs = $legs;
        $this->registration_number = $registration_number;
        $this->champion_number = $champion_number;
        $this->rabbit = $rabbit;
    }




    public function handle(PedigreeRepository $pedigrees, RabbitBreederRepository $breeders, RabbitKitRepository $kits,
                           BreederCategoryRepository $categories, LitterRepository $litters, CloudinaryImageHandler $handler)
    {
        // dd($this->pedigree);
        if ($breeder = $this->pedigree->meBreeder) {
            /* @var $breeder \App\Models\RabbitBreeder */
            // $updateBreeder = new UpdateRabbitBreederJob($breeder, $this->prefix, $this->name, $this->breed, $breeder->cage,
            //                                             $this->custom_id, $this->sex, $this->weight, $breeder->father_id,
            //                                             $breeder->mother_id, $this->color, $this->aquired,
            //                                             $this->day_of_birth, $this->image, $this->notes, $this->legs,
            //                                             $this->registration_number, $this->champion_number, $breeder->category_id);
            // $updateBreeder->handle($breeders, $categories, $handler);
            $this->image = $breeder->image['name'];
        } elseif ($this->pedigree->isKitHimself()) {
            $kit = $this->pedigree->kit;
            $weights = $kit->weight;
            if ($weight_changed = !is_array($weights)) {
                $weights = [$this->weight];
            } elseif ($weight_changed = end($weights) != $this->weight) {
                $weights[] = $this->weight;
            }
            $updateKit = new UpdateRabbitKitJob($kit, $kit->litter_id, $this->custom_id, $this->color, $this->sex, $weights,
                                                $this->image, $this->notes, $weight_changed, true);
            $updateKit->handle($kits, $handler, $litters);
            $this->image = $kit->image['name'];
        } else {
            $image       = $handler->prepareImageUsingTemp($this->image, 'pedigree');
            $this->image = $image['name'];
        }
        $rabbit = RabbitBreeder::where('id', $this->rabbit)->first();
        // dd($rabbit->pedigrees->toArray());
        // dd($this->pedigree->breeder->pedigrees->toArray());
        // $rabbitPedi = new RabbitBreeder;
        // $rabbitPedi->updatePedi($rabbit,$this->pedigree);
        $pedigrees->updateFromJob($this->pedigree, $this);

        return $this->pedigree;
    }
}
