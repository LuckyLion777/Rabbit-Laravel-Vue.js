<?php

namespace App\Jobs;

use App\Repositories\LedgerCategoryRepository;

class UpdateLedgerCategoryJob extends CreateLedgerCategoryJob
{
    public $category;

    public function __construct($category, $name, $description)
    {
        $this->category = $category;
        parent::__construct($name, $description);
    }

    /**
     * Execute the job.
     *
     * @param LedgerCategoryRepository $ledger
     * @return mixed
     */
    public function handle(LedgerCategoryRepository $ledger)
    {
        return $ledger->updateFromJob($this->category, $this);
    }
}
