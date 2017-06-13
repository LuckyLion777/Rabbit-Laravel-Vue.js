<?php

namespace App\Models\Filters;


use App\Http\Requests\GetLedgerRequest;
use App\Models\Ledger\Category;
use App\Models\Ledger\Entry;
use App\Models\RabbitBreeder;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Builder;

class LedgerFilter extends Filter
{
    protected $allowedFilters = [
        'associated_type',
        'associated_id',
        'order',
        'archived',
        'debit',
        'from',
        'to',
        'order',
        'orderDirection'
    ];

    protected $archived = false;
    protected $debit;
    protected $from;
    protected $to;

    protected $associated_type = null;
    protected $associated_id;

    protected $order;
    protected $orderDirection;

    protected $allowSort = [
        'date',
        'name',
        'amount',
    ];

    protected $filterRules = [
        'name' => 'string',
        'description' => 'string',
        'amount' => 'string',
        'category' => 'none'
    ];

    public function __construct(GetLedgerRequest $request, Cache $cache)
    {
        parent::__construct($request, $cache);
    }

    /**
     * @param Builder $entries
     * @param string $cacheName
     * @param number $perPage
     * @param number $total OUTPUT VARIABLE
     * @return mixed
     */
    public function filter($entries, $cacheName, $perPage)
    {
        $userDateFormat = \Auth::user()->getDateFormatPHP();
        $entries->archived($this->archived);
        if (isset($this->debit)) {
            $entries->where('debit', $this->debit);
        }
        if ($this->from) {
            $entries->where('date', '>=', Carbon::createFromFormat($userDateFormat, $this->from)->toDateString());
        }
        if ($this->to) {
            $entries->where('date', '<=', Carbon::createFromFormat($userDateFormat, $this->to)->toDateString());
        }

        $entries = $this->applyFilters($entries);
        $entries = $this->applySearch($entries);

        if(isset($this->request->filters['category'])) {
            if ($category = $this->request->filters['category']) {
                if($category != 'all'){
                    $entries->whereHas('category', function($query) use ($category) {
                        $query->where('id', $category);
                    });
                }
            }
        }

        if ($this->associated_type) {
            /* Special case for breeders - see ticket #88, showing also litters */
            if ($this->associated_type == Category::CATEGORY_BREEDER) {
                $entries->where(function(Builder $builder) {
                    $builder->where(function(Builder $sub) {
                        $sub->where([
                            'associated_type' => Category::CATEGORY_BREEDER,
                            'associated_id' => $this->associated_id,
                        ]);
                    })->orWhere(function(Builder $sub) {
                        $breeder = RabbitBreeder::find($this->associated_id);
                        /* @var $breeder RabbitBreeder */
                        $sub->where('associated_type', Category::CATEGORY_LITTER)
                            ->whereIn('associated_id', $breeder->litters->pluck('id'));
                    });
                });
            } else {
                $entries->where('associated_type', $this->associated_type)
                        ->where('associated_id', $this->associated_id);
            }
        }

        $cloned = clone $entries;


        if (in_array($this->order, $this->allowSort)) {
            $entries->orderBy($this->order, $this->orderDirection);
        } elseif ($this->order === 'category') {
            $entries->select(['ledger_entries.*', 'ledger_categories.name as cname'])
                    ->join('ledger_categories', 'category_id', '=', 'ledger_categories.id')
                    ->orderBy('cname', $this->orderDirection);
        } else {
            $entries->orderBy('date', 'desc');
        }

        return [$cloned->sum(\DB::raw('IF(`debit`, 1, -1) * `amount`')), $entries->paginate($perPage)];
    }
}
