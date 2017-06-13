<?php

namespace App\Repositories;

use App\Models\RabbitBreederCategory;

class BreederCategoryRepository extends Repository
{
    protected $createFromFields = ['name', 'description', 'user_id'];
    protected $updateFromFields = ['name', 'description'];

    /**
     * LodgerCategoryRepository constructor.
     * @param RabbitBreederCategory $category
     */
    public function __construct(RabbitBreederCategory $category)
    {
        $this->object = $category;
    }
}
