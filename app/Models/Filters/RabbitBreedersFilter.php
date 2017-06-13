<?php

namespace App\Models\Filters;


use App\Http\Requests\GetRabbitBreedersRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as Cache;

class RabbitBreedersFilter extends Filter
{
    protected $allowedFilters = [
        'sex',
        'archived',
        'sold',
        'butchered',
        'died',
        'order',
        'orderDirection'
    ];
    protected $sex;
    protected $archived;
    protected $sold;
    protected $butchered;
    protected $died;
    protected $order;

    protected $forNatSort = [
        'tattoo',
        'cage'
    ];

    protected $filterRules = [
        'name' => 'string',
        'tattoo' => 'string',
        'breed' => 'string',
        'cage' => 'string',
        'color' => 'string',
        'date_of_birth' => 'daterange',
        'aquired' => 'daterange',
        'notes' => 'string',
        'category_id' => 'number',

        'bred' => 'boolean',
    ];

    public function __construct(GetRabbitBreedersRequest $request, Cache $cache)
    {
        parent::__construct($request, $cache);
    }

    public function filter($breeders, $cacheName, $perPage)
    {
//        $this->cacheName = $cacheName . '.' . $this->cacheName;

//        return $this->cache->remember($this->cacheName, 10, function () use ($bands, $perPage) {
        $breeders
            ->sex($this->sex)
            ->archived($this->archived)
            ->butchered($this->butchered)
            ->died($this->died)
            ->sold($this->sold);

        $breeders = $this->applyFilters($breeders);
        $breeders = $this->applySearch($breeders);

        if (in_array($this->order, $this->forNatSort)) {
            $breeders->orderBy(\DB::raw('LENGTH(' . $this->order . ')'), 'asc')->orderBy($this->order, 'asc');
        } else if($this->order == 'date_of_birth' ){
            $breeders->orderBy(\DB::raw('-' . '`'.$this->order.'`'), $this->orderDirection);
        }
        else{
            $breeders->orderBy(\DB::raw($this->order), $this->orderDirection);
        }

        return $breeders->paginate($perPage);
    }
}
