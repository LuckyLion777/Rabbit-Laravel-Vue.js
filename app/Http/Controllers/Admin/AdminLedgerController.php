<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateLedgerEntryRequest;
use App\Http\Requests\GetLedgerRequest;
use App\Http\Requests\UpdateLedgerEntryRequest;
use App\Jobs\CreateLedgerEntryJob;
use App\Jobs\UpdateLedgerEntryJob;
use App\Models\Filters\LedgerFilter;
use App\Http\Controllers\Controller;
use App\Models\Ledger\Entry;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Guard;
use Collective\Bus\Dispatcher;
use Illuminate\Http\Request;
use App\Models\Ledger\Category as LedgerCategory;

class AdminLedgerController extends Controller
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->middleware('protect:3,entries', ['except' => ['index', 'store', 'statistics', 'autocomplete', 'saveLedgers']]);
    }

    public function index(LedgerFilter $filter, GetLedgerRequest $request, Guard $auth)
    {
        $user = $auth->user();
        list($total, $ledger) = $filter->filter($user->ledger()->with(['category', 'associated']),
                                    'allLedger', 20);
        return response()->json(compact('ledger', 'total') + [
            'order' => $request->get('order') . '|' . $request->get('orderDirection'),
        ]);
    }
    
    public function autocomplete(Guard $auth)
    {
        $fields = ['name', 'amount', 'category_name'];
        $data = [];
        $ledger = $auth->user()->ledger->load('category');
        foreach ($fields as $field) {
            $data[$field] = $ledger->pluck($field)->unique()->values();
        }
        return response()->json($data);
    }
    
    public function statistics(Request $request, Guard $auth)
    {
        return response()->json($auth->user()->getLedgerStatistics(!!$request->get('archived')));
    }

    public function store(CreateLedgerEntryRequest $request)
    {
        return $this->dispatcher->dispatchFrom(CreateLedgerEntryJob::class, $request)->fresh('category');
    }

    public function update(Entry $entries, UpdateLedgerEntryRequest $request)
    {
        $request['entry'] = $entries;
        return $this->dispatcher->dispatchFrom(UpdateLedgerEntryJob::class, $request)->fresh('category');
    }

    public function archive(Entry $entries, Request $request) {
        $entries->archived_at = $request->get('archived') ? Carbon::now() : null;
        $entries->update();
        return $entries->fresh('category');
    }

    public function destroy(Entry $entries)
    {
        $entries->delete();
    }
    
    public function saveLedgers(Request $request)
    {
    	\DB::beginTransaction();
    	$category = LedgerCategory::where('special', 'general')->first();
    	$categoryId = $category->id;
    	$fields = ['debit', 'date', 'name', 'amount', 'description', 'category_id'];
    	$emptyLedger = $request->get('emptyLedger');
    	foreach ($request->get('ledgers') as $ledgerData) {
    		if($ledgerData['date']) {
    			array_walk($ledgerData, function(&$val, $key) use ($fields, $emptyLedger, $categoryId){
    				if($key == 'category_id')
    				{
    					$val = $categoryId;
    				}
    				else
    				{
    					$val =  in_array($key, $fields) ? $val : $emptyLedger[$key];
    				}
    			});
    			$this->dispatcher->dispatchFromArray(CreateLedgerEntryJob::class, $ledgerData);
    		}
    	}
    	\DB::commit();
    	return response()->json([]);
    }
}
