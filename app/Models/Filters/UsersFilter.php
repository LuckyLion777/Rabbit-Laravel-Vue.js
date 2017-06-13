<?php

namespace App\Models\Filters;


use App\Http\Requests\GetRabbitBreedersRequest;
use App\Http\Requests\GetUsersRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as Cache;

class UsersFilter extends Filter
{
    protected $allowedFilters = [

    ];

    protected $filterRules = [
        'id' => 'number',
        'name' => 'string',
        'email' => 'string',
        'stripe_id' => 'string',
        'trial_ends_at' => 'daterange',
        'subscription_status' => 'none'
    ];

    public function __construct(GetUsersRequest $request, Cache $cache)
    {
        parent::__construct($request, $cache);
    }

    public function filter($items, $cacheName, $perPage)
    {


        $items = $this->applyFilters($items);

        if(isset($this->request->filters)){
            if($subscription = $this->request->filters['subscription_status']){
                switch($subscription) {
                    case 'all':
                        break;
                    case 'on trial':
                        $items->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', Carbon::now());
                        break;
                    case 'expired':
                        $items->where('trial_ends_at', '<=', Carbon::now()->startOfDay())->where(function ($query) {
                            $query->whereHas('subscriptions', function($query){
                                $query->whereNotNull('ends_at')->where('ends_at', '<', Carbon::now());
                            })->orHas('subscriptions', '=', 0);
                        });
                        break;
                    case 'premium_yr':
                        $items->where(function ($query) {
                            $query->whereHas('subscriptions', function($query){
                                $query->where(function($query){
                                    $query->where('ends_at', null)->orWhere('ends_at', '>=', Carbon::now()->startOfDay());
                                });
                                $query->where('stripe_plan', 'premium_yr');
                            });
                        });
                        break;
                    case 'basic_yr':
                        $items->where(function ($query) {
                            $query->whereHas('subscriptions', function($query){
                                $query->where(function($query){
                                    $query->where('ends_at', null)->orWhere('ends_at', '>=', Carbon::now()->startOfDay());
                                });
                                $query->where('stripe_plan', 'basic_yr');
                            });
                        });
                        break;
                    case 'mini_yr':
                        $items->where(function ($query) {
                            $query->whereHas('subscriptions', function($query){
                                $query->where(function($query){
                                    $query->where('ends_at', null)->orWhere('ends_at', '>=', Carbon::now()->startOfDay());
                                });
                                $query->where('stripe_plan', 'mini_yr');
                            });
                        });
                        break;
                    case 'forever':
                        $items->where(function ($query) {
                            $query->whereHas('subscriptions', function($query){
                                $query->where(function($query){
                                    $query->where('ends_at', null)->orWhere('ends_at', '>=', Carbon::now()->startOfDay());
                                });
                                $query->where('stripe_plan', 'forever');
                            });
                        });
                        break;
                }
            }
        }

        $items = $this->applySearch($items);

        return $items->paginate($perPage);
    }
}
