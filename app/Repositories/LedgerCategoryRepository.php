<?php

namespace App\Repositories;

use App\Models\Ledger\Category;

class LedgerCategoryRepository extends Repository
{
    protected $createFromFields = ['name', 'description', 'user_id'];
    protected $updateFromFields = ['name', 'description'];

    /**
     * LodgerCategoryRepository constructor.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->object = $category;
    }
}
