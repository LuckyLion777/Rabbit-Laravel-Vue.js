<?php

namespace App\Jobs;

use App\Events\EventHolderWasRenamed;
use App\Handlers\CloudinaryImageHandler;
use App\Handlers\ImageHandler;
use App\Jobs\Job;
use App\Models\RabbitBreederCategory;
use App\Repositories\BreederCategoryRepository;
use App\Repositories\RabbitBreederRepository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateRabbitBreederJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;
    /**
     * @var
     */
    public $breeder;
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
    public $breed;
    /**
     * @var
     */
    public $cage;
    /**
     * @var
     */
    public $tattoo;
    /**
     * @var
     */
    public $sex;
    /**
     * @var
     */
    public $weight;
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
    public $color;
    /**
     * @var
     */
    public $aquired;
    /**
     * @var
     */
    public $date_of_birth;
    /**
     * @var
     */
    public $image;
    /**
     * @var
     */
    public $notes;

    public $legs;

    public $registration_number;

    public $champion_number;

    public $category_id;

    public $category_name;

    /**
     * Create a new job instance.
     *
     * @param $breeder
     * @param $prefix
     * @param $name
     * @param $breed
     * @param $cage
     * @param $tattoo
     * @param $sex
     * @param $weight
     * @param $father_id
     * @param $mother_id
     * @param $color
     * @param $aquired
     * @param $date_of_birth
     * @param $image
     * @param $notes
     * @param $legs
     * @param $registration_number
     * @param $champion_number
     * @param $category_id
     * @param $category_name
     */
    public function __construct($breeder, $prefix, $name, $breed, $cage, $tattoo, $sex, $weight, $father_id, $mother_id, $color,
                                $aquired, $date_of_birth, $image, $notes, $legs, $registration_number, $champion_number,
                                $category_id, $category_name = null)
    {
        $this->breeder   = $breeder;
        $this->prefix    = $prefix;
        $this->name      = $name;
        $this->breed     = $breed;
        $this->cage      = $cage;
        $this->tattoo    = $tattoo;
        $this->sex       = $sex;
        $this->weight    = $weight;
        $this->father_id = $father_id;
        $this->mother_id = $mother_id;
        $this->color     = $color;
        $this->aquired   = $aquired;
        $this->image     = $image;
        $this->notes     = $notes;
        $this->date_of_birth   = $date_of_birth;
        $this->legs = $legs;
        $this->registration_number = $registration_number;
        $this->champion_number = $champion_number;
        $this->category_id = $category_id;
        $this->category_name = $category_name;
    }

    protected function createCategory(BreederCategoryRepository $categoriesRepository)
    {
        $categories = RabbitBreederCategory::where('name', $this->category_name)->where(function(Builder $builder) {
            $builder->where('user_id', auth()->id())->orWhereNotNull('special');
        });
        if ($category = $categories->first()) {
            $this->category_id = $category->id;
            return;
        }

        $createCategory = new CreateBreederCategoryJob($this->category_name, null);
        $this->category_id = $createCategory->handle($categoriesRepository)->id;
    }

    public function handle(RabbitBreederRepository $breeders, BreederCategoryRepository $categories,
                           CloudinaryImageHandler $handler)
    {
        $image       = $handler->prepareImageUsingTemp($this->image, 'breeders');
        $this->image = $image['name'];

        if ($this->name != $this->breeder->name) {
            \Event::fire(new EventHolderWasRenamed($this->breeder, $this->name));
        }

        if (!$this->category_id) {
            $this->createCategory($categories);
        }

        return $breeders->updateFromJob($this->breeder, $this);
    }
}
