<?php

namespace App\Jobs;

use App\Repositories\BreederCategoryRepository;

class UpdateBreederCategoryJob extends CreateBreederCategoryJob
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
     * @param BreederCategoryRepository $ledger
     * @return mixed
     */
    public function handle(BreederCategoryRepository $ledger)
    {
        return $ledger->updateFromJob($this->category, $this);
    }
}
