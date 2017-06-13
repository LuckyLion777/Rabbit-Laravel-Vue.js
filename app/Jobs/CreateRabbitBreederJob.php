<?php

namespace App\Jobs;

use App\Handlers\CloudinaryImageHandler;
use App\Handlers\ImageHandler;
use App\Jobs\Job;
use App\Models\RabbitBreeder;
use App\Models\RabbitBreederCategory;
use App\Repositories\BreederCategoryRepository;
use App\Repositories\RabbitBreederRepository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateRabbitBreederJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;
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
    /**
     * @var
     */
    public $weight;

    public $category_id;

    public $category_name;

    public $legs;

    public $registration_number;

    public $champion_number;
    
    public $archived;
    
    public $died;
    
    public $died_at;
    
    public $butchered;
    
    public $butchered_at;
    
    public $sold_at;
    
    public $father_name;
    
    public $mother_name;

    /**
     * Create a new job instance.
     *
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
    public function __construct($prefix = '', $name, $breed, $cage, $tattoo, $sex, $weight, $father_id, $mother_id, $color, $aquired,
                                $date_of_birth, $image, $notes, $legs, $registration_number, $champion_number,
                                $category_id, $category_name = null, $archived = 0, $died = 0, $died_at = null, 
    							$butchered = 0, $butchered_at = null, $sold_at = null, $father_name = null, $mother_name = null)
    {
        $this->prefix      = $prefix;
        $this->name      = $name;
        $this->breed     = $breed;
        $this->cage      = $cage;
        $this->tattoo    = $tattoo;
        $this->sex       = $sex;
        $this->father_id = $father_id;
        $this->mother_id = $mother_id;
        $this->color     = $color;
        $this->aquired   = $aquired;
        $this->image     = $image;
        $this->notes     = $notes;
        $this->weight    = $weight;
        $this->date_of_birth   = $date_of_birth;
        $this->legs = $legs;
        $this->registration_number = $registration_number;
        $this->champion_number = $champion_number;
        $this->category_id = $category_id;
        $this->category_name = $category_name;
        $this->archived = $archived;
        $this->died = $died;
        $this->died_at = $died_at;
        $this->butchered = $butchered;
        $this->butchered_at = $butchered_at;
        $this->sold_at = $sold_at;
        $this->father_name = $father_name;
        $this->mother_name = $mother_name;
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

    /**
     * Execute the job.
     *
     * @param RabbitBreederRepository $breeders
     * @param BreederCategoryRepository $categories
     * @param CloudinaryImageHandler|ImageHandler $handler
     * @return mixed
     */
    public function handle(RabbitBreederRepository $breeders, BreederCategoryRepository $categories,
                           CloudinaryImageHandler $handler)
    {
        if(!empty($this->father_name))
        {
        	$father = RabbitBreeder::where('name', 'LIKE', $this->father_name)->get()->last();
        	if(!empty($father))
        	{
        		$this->father_id = $father->id;
        	}
        }
    	if(!empty($this->mother_name))
    	{
    		$mother = RabbitBreeder::where('name', 'LIKE', $this->mother_name)->get()->last();
    		if(!empty($mother))
    		{
    			$this->mother_id = $mother->id;
    		}
    	}
    	$image       = $handler->prepareImageUsingTemp($this->image, 'breeders');
        $this->image = $image['name'];
        if (!$this->category_id) {
            $this->createCategory($categories);
        }

        $breeder = $breeders->createFromJob($this);

        auth()->user()->breeders()->save($breeder);

        return $breeder;

    }
}
