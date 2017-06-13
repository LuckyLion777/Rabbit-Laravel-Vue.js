<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBreederCategoryRequest;
use App\Http\Requests\UpdateBreederCategoryRequest;
use App\Jobs\CreateBreederCategoryJob;
use App\Jobs\UpdateBreederCategoryJob;
use App\Models\RabbitBreederCategory;
use Collective\Bus\Dispatcher;
use Illuminate\Contracts\Auth\Guard;

class AdminRabbitBreederCategoriesController extends Controller
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
        $categories = $auth->user()->getBreederCategories();
        return response()->json(['categories' => $categories]);
    }

    public function autocomplete(Guard $auth)
    {
        $fields = ['name'];
        $data = [];
        $categories = $auth->user()->getBreederCategories();
        foreach ($fields as $field) {
            $data[$field] = $categories->pluck($field)->unique()->values();
        }
        return response()->json($data);
    }

    public function store(CreateBreederCategoryRequest $request)
    {
        return $this->dispatcher->dispatchFrom(CreateBreederCategoryJob::class, $request);
    }

    public function update(RabbitBreederCategory $categories, UpdateBreederCategoryRequest $request)
    {
        $request['category'] = $categories;
        return $this->dispatcher->dispatchFrom(UpdateBreederCategoryJob::class, $request);
    }

    public function destroy(RabbitBreederCategory $categories)
    {
        $categories->delete();
    }
}
