<?php

namespace App\Http\Controllers\Admin;

use App\Models\RabbitBreeder;
use App\Models\RabbitKit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Collective\Bus\Dispatcher;
use App\Models\Pedigree;
use App\Http\Requests\UpdatePedigreeRequest;
use App\Jobs\UpdatePedigreeJob;

class AdminPedigreesController extends Controller
{
    //
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * RabbitBreedersController constructor.
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->middleware('protect', ['only' => ['update', 'destroy','archive','show','getLitters','events']]);
    }

    public function show(Pedigree $pedigree)
    {
        //$breeder->load('father', 'mother','user');
        return $pedigree;
    }

    public function update(Pedigree $pedigree, UpdatePedigreeRequest $request)
    {
        $request['pedigree'] = $pedigree;

        $updatedBreeder = $this->dispatcher->dispatchFrom(UpdatePedigreeJob::class, $request);
        //$updatedBreeder->load('mother', 'father');

        return $updatedBreeder;
    }

    public function copyOptions(Request $request)
    {
        if (!($user = $request->user())) {
            return response()->json(['options' => []]);
        }
        /* @var $user \App\Models\User */
        $collection = $request->query('type', 'breeder') == 'breeder' ? $user->breeders() : $user->rabbitKits();
        if ($request->query('archived') != 'true') {
            if ($request->query('type', 'breeder') == 'breeder') {
                $collection = $collection->active();
            } else {
                $collection->whereNull('sold_at')->where('alive', 1)->where('archived', 0);
            }
        }
        $collection->where('id', '<>', $request->query('exclude'));
        return response()->json(['options' => $collection->get()->map(function (Model $model) {
            if ($model instanceof RabbitBreeder) {
                return ['id' => $model->id, 'title' => $model->name . ': ' . $model->tattoo];
            }
            if ($model instanceof RabbitKit) {
                return ['id' => $model->id, 'title' => $model->given_id];
            }
        })]);
    }

    public function copy(Request $request)
    {
        $from = $request->request->get('type', 'breeder') == 'breeder'
            ? RabbitBreeder::findOrFail($request->request->get('from'))
            : RabbitKit::findOrFail($request->request->get('from'));
        $to = $request->request->get('type', 'breeder') == 'breeder'
            ? RabbitBreeder::findOrFail($request->request->get('to'))
            : RabbitKit::findOrFail($request->request->get('to'));

        if ($to->user->id != $request->user()->id) {
            return response('Unauthorized', 403);
        }

        /* @var RabbitBreeder|RabbitKit $from */
        /* @var RabbitBreeder|RabbitKit $to */

        switch ($request->request->get('line', 'both')) {
            case 'father':
                $condition = function ($query) {
                    /* @var $query Builder|QueryBuilder */
                    $query->whereIn('level', ['g2.f1', 'g3.f1', 'g3.m1', 'g4.f1', 'g4.m1', 'g4.f2', 'g4.m2']);
                };
                break;
            case 'mother':
                $condition = function ($query) {
                    /* @var $query Builder|QueryBuilder */
                    $query->whereIn('level', ['g2.m1', 'g3.f2', 'g3.m2', 'g4.f3', 'g4.m3', 'g4.f4', 'g4.m4']);
                };
                break;
            case 'both':
            default:
                $condition = false;
        }

        $pedigrees = $from->pedigrees();
        $pedigrees->when($condition, $condition);

        \DB::statement("CREATE TEMPORARY TABLE `temp_table` {$pedigrees->toSql()}", $pedigrees->getBindings());

        $field = $request->request->get('type', 'breeder') == 'breeder' ? 'rabbit_breeder_id' : 'rabbit_kit_id';
        \DB::table('temp_table')->update([$field => $to->id]);
        \Schema::table('temp_table', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        $to->pedigrees()->when($condition, $condition)->delete();
        \DB::insert("INSERT INTO `pedigrees` SELECT NULL AS `id`, `temp_table`.* FROM `temp_table`");
    }
}
