<?php

namespace App\Models\Filters;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Request;
use Carbon\Carbon;

abstract class Filter
{
    protected $allowedFilters;
    protected $cache;
    protected $cacheName;
    protected $request;

    public function __construct(Request $request, Cache $cache)
    {
        foreach ($this->allowedFilters as $filter) {
            $filterVal = $request->get($filter);
            if ($filterVal !== null) {
                $this->{$filter} = str_contains($filterVal, ',') ? explode(',', $filterVal) : $filterVal;
            }

            if ($request->has($filter))
                $this->cacheName .= $filter . $filterVal . '.';
        }
        $this->cache = $cache;

        $this->request = $request;
    }

    public abstract function filter($items, $cacheName, $perPage);

    public function applyFilters($items)
    {
        if (isset($this->request->filters)) {
            foreach ($this->request->filters as $key => $value) {
                if ($this->filterRules[$key] == 'string') {
                    if ($value != "") {
                        $items->where($key, 'like', '%' . $value . '%');
                    }
                }
                if ($this->filterRules[$key] == 'number') {
                    if ($value != "") {
                        $items->where($key, $value);
                    }
                }
                if ($this->filterRules[$key] == 'daterange') {
                    $userDateFormat = \Auth::user()->getDateFormatPHP();
                    if ($value['from'] != "") {
                        $items->where($key, '>=', Carbon::createFromFormat($userDateFormat, $value['from'])
                                                            ->startOfDay());
                    }
                    if ($value['to'] != "") {
                        $items->where($key, '<=', Carbon::createFromFormat($userDateFormat, $value['to'])
                                                            ->startOfDay());
                    }
                }
                if (substr( $this->filterRules[$key], 0, 8 ) === 'relation'){
                    $relationKey = explode('.', $this->filterRules[$key]);
                    if ($value != "") {
                        $items->whereHas($relationKey[1], function ($q) use ($relationKey, $value){
                            $q->where($relationKey[2], 'like', '%' . $value . '%');
                        });
                    }
                }
            }
        }
        return $items;
    }

    public function applySearch($items){
        if($this->request->searchQuery != '') {
            $items->where(function ($query) {
                foreach ($this->filterRules as $key => $value) {
                    if ($value == 'string') {
                        $query->orWhere($key, 'like', '%' . $this->request->searchQuery . '%');
                    }
                }
            });
        }
        return $items;
    }

}
