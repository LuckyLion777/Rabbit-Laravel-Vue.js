<?php

namespace App\Jobs;

use App\Repositories\LedgerCategoryRepository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class CreateLedgerCategoryJob extends Job implements SelfHandling
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
     * @param LedgerCategoryRepository $ledger
     * @return mixed
     */
    public function handle(LedgerCategoryRepository $ledger)
    {
        return $ledger->createFromJob($this);
    }
}
