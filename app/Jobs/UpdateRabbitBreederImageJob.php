<?php

namespace App\Jobs;

use App\Events\EventHolderWasRenamed;
use App\Handlers\CloudinaryImageHandler;
use App\Handlers\ImageHandler;
use App\Jobs\Job;
use App\Repositories\RabbitBreederRepository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateRabbitBreederImageJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;
    /**
     * @var
     */
    public $breeder;

    /**
     * @var
     */
    public $image;


    /**
     * Create a new job instance.
     *
     * @param $breeder
     * @param $image
     */
    public function __construct($breeder, $image)
    {
        $this->breeder   = $breeder;
        $this->image     = $image;
    }


    public function handle(RabbitBreederRepository $breeders, CloudinaryImageHandler $handler)
    {
        $image       = $handler->prepareImageUsingTemp($this->image, 'breeders');
        $this->image = $image['name'];
        $this->breeder->image = $this->image;
        $this->breeder->save();

        return $this->breeder;
    }
}
