<?php

namespace App\Models\Filters;


use App\Http\Requests\GetRabbitBreedersRequest;
use App\Http\Requests\GetUsersRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as Cache;

class TasksFilter extends Filter
{
    protected $allowedFilters = [

    ];

    protected $filterRules = [
        'name' => 'string',
        'holderName' => 'string'
    ];

    public function __construct(GetUsersRequest $request, Cache $cache)
    {
        parent::__construct($request, $cache);
    }

    public function filter($items, $cacheName, $perPage)
    {
        $items = $this->applySearch($items);
        return $items->paginate($perPage);
    }
}
