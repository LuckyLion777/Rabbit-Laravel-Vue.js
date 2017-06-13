<?php

namespace App\Http\Controllers\Admin;

use App\Events\LitterWasArchived;
use App\Events\LitterWasDeleted;
use App\Events\LitterWasUnarchived;
use App\Events\LitterWasWeighed;
use App\Http\Requests\ArchiveRequest;
use App\Http\Requests\CreateLitterRequest;
use App\Http\Requests\UpdateLitterRequest;
use App\Http\Requests\WeightLitterRequest;
use App\Jobs\CreateLitterJob;
use App\Jobs\UpdateLitterJob;
use App\Jobs\UpdateRabbitKitJob;
use App\Jobs\WeightKitJob;
use App\Models\BreedPlan;
use App\Models\Filters\LittersFilter;
use App\Models\Ledger\Category;
use App\Models\Ledger\Entry;
use App\Models\Litter;
use Carbon\Carbon;
use Collective\Bus\Dispatcher;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AdminLittersController extends Controller
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * AdminLittersController constructor.
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->middleware('protect', ['except' => ['index', 'store','getList']]);
    }

    /**
     * @param LittersFilter $filter
     * @return mixed
     */
    public function index(LittersFilter $filter, Requests\GetLittersRequest $request)
    {
        $user    = auth()->user();
        $litters = $filter->filter($user->litters(), 'user' . $user->id . 'litters', getenv('LITTERS_PER_PAGE'));
        foreach ($litters as $litter) {
            $litter->load('parents');
            $litter->setAge();
        }

        return response()->json(['litters' => $litters, 'order' => $request->get('order') . '|' . $request->get('orderDirection')]);
    }

    public function getKits(Litter $litter)
    {
        return $litter->survivedKits;
    }

    public function getDied(Litter $litter)
    {
        return $litter->diedKits;
    }

    public function store(CreateLitterRequest $request)
    {
        return $this->dispatcher->dispatchFrom(CreateLitterJob::class, $request);
    }

    public function update(Litter $litter, UpdateLitterRequest $request)
    {
        $request['litter'] = $litter;

        return $this->dispatcher->dispatchFrom(UpdateLitterJob::class, $request);
    }

    public function show(Litter $litter)
    {
        $litter->load(['parents', 'user']);
        $litter->weighs    = $litter->weighs()->count();
        if($mother = $litter->parents()->where('sex', '=', 'doe')->first()){
            $litter->mother_id = $mother->id;
        }
        if($father = $litter->parents()->where('sex', '=', 'buck')->first()){
            $litter->father_id = $father->id;
        }

        $litter->kitsForButchCount = $litter->kitsForButchCount();
        $litter->kitsButcheredCount = $litter->kitsButchered()->count();

        return $litter;
    }

    /**
     * Weights litter.
     * As we can have multiple types of animals we should
     * pass additional param "animal_type"
     * @param Litter $litter
     * @return mixed
     */
    public function weigh(Litter $litter)
    {
        return $litter->rabbitKits()->archived(0)->sold(false)->get();
    }

    public function postWeigh(Litter $litter, WeightLitterRequest $request)
    {
        if ($litter->weighs()->count()) {
            foreach ($request->get('kits') as $kitWeigh) {
                $kits = \App::make('rabbitkit');
                $kit  = $kits->find($kitWeigh['id']);
                $this->dispatcher->dispatchFromArray(WeightkitJob::class, ['kit' => $kit, 'current_weight' => $kitWeigh['current_weight'], 'current_weight_date' => $kitWeigh['current_weight_date']]);
            }
        } else {
            foreach ($request->get('kits') as $kitUpdate) {
                $kits = \App::make('rabbitkit');
                $kit  = $kits->find($kitUpdate['id']);
                $kitUpdate['kit'] = $kit;
                $kitUpdate['first'] = true;
                $this->dispatcher->dispatchFromArray(UpdateRabbitKitJob::class, $kitUpdate);
                $this->dispatcher->dispatchFromArray(WeightkitJob::class, ['kit' => $kit, 'current_weight' => $kitUpdate['current_weight'],  'current_weight_date' => $kitUpdate['current_weight_date'], 'first' => true]);
            }
        }
        event(new LitterWasWeighed($litter, $request->get('date')));
    }

    /**
     * @param Litter $litter
     * @param ArchiveRequest $request
     */
    public function archive(Litter $litter, ArchiveRequest $request)
    {
        $litter->archived = $request->get('archived');
        $litter->update();
        if($request->get('archived') == 0){
            event(new LitterWasUnarchived($litter, 'rabbit'));
        } else {
            event(new LitterWasArchived($litter, 'rabbit'));
        }
    }

    public function destroy(Litter $litter)
    {
        event(new LitterWasDeleted($litter, 'rabbit'));
        $litter->delete();
    }

    public function getList(LittersFilter $filter)
    {
        $user    = auth()->user();
        $litters = $filter->filter($user->litters(), 'user' . $user->id . 'litters', '-1');
        foreach ($litters as $litter) {
            $litter->load('parents');
            $litter->setAge();
        }

        return $litters;
    }

    public function events(Litter $litter, Request $request)
    {
        if ($request->has('weekStart')) {
            $date = Carbon::createFromFormat(\Auth::user()->getDateFormatPHP(), $request->get('weekStart'));
            return $litter->dateWeeklyEvents($date)->get();
        }
        return $litter->events;
    }

    public function butcherValue(Litter $litters, Request $request, Guard $auth)
    {
        $entry = new Entry();
        $entry->name = 'Butcher: ' . $litters->given_id;
        $entry->date = Carbon::now()->format($auth->user()->getDateFormatPHP());
        $entry->category()->associate(Category::where('special', 'litter')->first());
        $entry->user()->associate($auth->user());
        $entry->debit = true;
        $entry->amount = $request->get('value');
        $litters->ledgerEntries()->save($entry);
    }
}

