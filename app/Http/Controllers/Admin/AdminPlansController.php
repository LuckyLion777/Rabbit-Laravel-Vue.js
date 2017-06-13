<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\UpdateBreedPlanJob;
use App\Jobs\MissedBreedPlanJob;
use App\Models\BreedPlan;
use App\Models\RabbitBreeder;
use Carbon\Carbon;
use Collective\Bus\Dispatcher;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Http\Requests\MakeBreedPlanRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Jobs\CreateBreedPlanJob;
use Illuminate\Http\Response;

class AdminPlansController extends Controller
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
        $this->middleware('protect', ['except' => ['index', 'store', 'breeders']]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $plans = auth()->user()->plans()->whereDoesntHave('events', function ($query) {
            $query->where('type', '=', 'litter')->where('icon', 'fa-balance-scale bg-yellow')->whereNotNull('holderName');
        });
        $plans->with('breeders')->where('missed', 0)->orderBy('date', 'DESC');
        if ($request->has('perPage')) {
            return $plans->paginate($request->get('perPage'));
        }
        return $plans->get();

    }

    public function store(MakeBreedPlanRequest $request)
    {
        $this->dispatcher->dispatchFrom(CreateBreedPlanJob::class, $request);
    }

    public function update(BreedPlan $plan, MakeBreedPlanRequest $request)
    {
        $request['plan'] = $plan;
        return $this->dispatcher->dispatchFrom(UpdateBreedPlanJob::class, $request);
    }


    public function events(BreedPlan $plan, Request $request)
    {
        return $plan->events;
    }

    public function destroy(BreedPlan $plan)
    {
        $plan->events()->delete();
        $plan->delete();
    }

    public function missed(BreedPlan $plan, Request $request)
    {
        $request['plan'] = $plan;
        return $this->dispatcher->dispatchFrom(MissedBreedPlanJob::class, $request);
    }

    public function breeders()
    {
        $breeders = auth()->user()->breeders()->select(['id', 'name', 'archived', 'butchered', 'died', 'sold_at', 'tattoo', 'cage', 'color', 'sex', 'breed', 'user_id'])->get();
        $males    = $breeders->where('sex', 'buck')->flatten();
        $females  = $breeders->where('sex', 'doe')->flatten();

        return response()->json([
            'bucks' => $males,
            'does'  => $females,
        ], 200);
    }

    public function nextLitterId(BreedPlan $plan)
    {
        /** @var RabbitBreeder $doe */
        $doe = $plan->breeders()->where('sex', 'doe')->first();

        $nextLitterId = '';

        if ($doe) {
            $lastLitter = $doe->litters()->orderBy('born', 'DESC')->orderBy('id', 'DESC')->first();
            if ($lastLitter) {
                $id = $lastLitter->given_id;
                if (preg_match_all('/\d+/', $id, $numbers)) {
                    $lastnum = end($numbers[0]);
                    $base = substr( $id, 0, strrpos( $id, $lastnum));

                    $nextLitterId =  $base . ((int)$lastnum + 1);
                } else {
                    $nextLitterId = $id . '2';
                }
            }
        }

        return new Response([
            'nextLitterId' => $nextLitterId,
            'doe' => $doe
        ]);
    }
}

