<?php

namespace App\Models\Filters;

use App\Http\Requests\GetLittersRequest;
use App\Models\RabbitBreeder;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as Cache;

class LittersFilter extends Filter
{
    protected $allowedFilters = [
        'archived',
        'butchered',
        'order',
        'orderDirection'
    ];
    protected $butchered;
    protected $archived;
    protected $order;
    protected $orderDirection;

    protected $forNatSort = [
        'given_id',
    ];

    protected $filterRules = [
        'given_id' => 'string',
        'born' => 'daterange',
        'bred' => 'daterange',
        'notes' => 'string',
        'buck' => 'none',
        'doe' => 'none',
    ];

    public function __construct(GetLittersRequest $request, Cache $cache)
    {
        parent::__construct($request, $cache);
    }
    public function filter($items, $cacheName, $perPage)
    {
        $items = $this->applyFilters($items);
        $items = $this->applySearch($items);
        
        
        if (in_array($this->order, $this->forNatSort)) {
            $items->orderBy(\DB::raw('LENGTH(' . $this->order . ')'), 'asc')->orderBy($this->order, 'asc');
        } else {
            $items->orderBy($this->order, $this->orderDirection);
        }

        if(isset($this->request->filters['buck'])) {
            if ($buck = $this->request->filters['buck']) {
                if($buck != ''){
                    $items->whereHas('parents', function($query) use ($buck) {
                        $query->where('id', $buck);
                    });
                }
            }
        }

        if(isset($this->request->filters['doe'])) {
            if ($doe = $this->request->filters['doe']) {
                if($doe != ''){
                    $items->whereHas('parents', function($query) use ($doe) {
                        $query->where('id', $doe);
                    });
                }
            }
        }

        if($this->butchered){
            $items->butchered($this->butchered);
            $items->archived(1);
        } else {
            if($this->archived){
                $items->butchered(0);
            }
            $items->archived($this->archived);
        }

        if($perPage >= 0){
            return $items->paginate($perPage);
        } else {
            return $items->get();
        }
//        });
    }
}