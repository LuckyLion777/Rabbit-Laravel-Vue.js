<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLedgerCategoryRequest;
use App\Http\Requests\UpdateLedgerCategoryRequest;
use App\Jobs\CreateLedgerCategoryJob;
use App\Jobs\UpdateLedgerCategoryJob;
use App\Models\Ledger\Category;
use Collective\Bus\Dispatcher;
use Illuminate\Contracts\Auth\Guard;

class AdminCategoriesController extends Controller
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->middleware('protect:3,categories', ['except' => ['index', 'store', 'autocomplete']]);
    }
    public function index(Guard $auth)
    {
        $categories = $auth->user()->getLedgerCategories();
        return response()->json(['categories' => $categories]);
    }
    
    public function autocomplete(Guard $auth)
    {
        $fields = ['name'];
        $data = [];
        $categories = $auth->user()->getLedgerCategories();
        foreach ($fields as $field) {
            $data[$field] = $categories->pluck($field)->unique()->values();
        }
        return response()->json($data);
    }

    public function store(CreateLedgerCategoryRequest $request)
    {
        return $this->dispatcher->dispatchFrom(CreateLedgerCategoryJob::class, $request);
    }

    public function update(Category $categories, UpdateLedgerCategoryRequest $request)
    {
        $request['category'] = $categories;
        return $this->dispatcher->dispatchFrom(UpdateLedgerCategoryJob::class, $request);
    }
    
    public function destroy(Category $categories)
    {
        $categories->delete();
    }
}
