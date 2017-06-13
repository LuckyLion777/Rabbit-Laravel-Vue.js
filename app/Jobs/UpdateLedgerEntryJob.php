<?php

namespace App\Jobs;

use App\Repositories\LedgerCategoryRepository;
use App\Repositories\LedgerEntryRepository;

class UpdateLedgerEntryJob extends CreateLedgerEntryJob
{
    public $entry;

    public function __construct($entry, $name, $date, $category_id, $debit, $amount, $description, $category_name = null, $associated_id = null)
    {
        $this->entry = $entry;
        parent::__construct($name, $date, $category_id, $debit, $amount, $description, $category_name, $associated_id);
    }

    /**
     * Execute the job.
     *
     * @param LedgerEntryRepository $ledger
     * @param LedgerCategoryRepository $categories
     * @return mixed
     */
    public function handle(LedgerEntryRepository $ledger, LedgerCategoryRepository $categories)
    {
        if (!$this->category_id) {
            $this->createCategory($categories);
        }
        if ($this->associated_id) {
            $this->figureAssociation();
        }
        
        return $ledger->updateFromJob($this->entry, $this);
    }
}
