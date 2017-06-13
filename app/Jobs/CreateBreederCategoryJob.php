<?php

namespace App\Jobs;

use App\Repositories\BreederCategoryRepository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class CreateBreederCategoryJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $description;
    /**
     * @var int
     */
    public $user_id;

    /**
     * Create a new job instance.
     * @param $name
     * @param $description
     */
    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;

        $this->user_id = auth()->user()->id;
    }

    /**
     * Execute the job.
     *
     * @param BreederCategoryRepository $ledger
     * @return mixed
     */
    public function handle(BreederCategoryRepository $ledger)
    {
        return $ledger->createFromJob($this);
    }
}
