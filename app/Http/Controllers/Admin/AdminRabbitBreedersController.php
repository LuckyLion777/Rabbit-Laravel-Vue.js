<?php

namespace App\Http\Controllers\Admin;

use App\Events\RabbitBreederWasArchived;
use App\Events\RabbitBreederWasButched;
use App\Events\RabbitBreederWasSold;
use App\Events\RabbitBreederWasUnbutched;
use App\Http\Requests\ArchiveRequest;
use App\Http\Requests\CreateRabbitBreederRequest;
use App\Http\Requests\SoldRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Requests\UpdateRabbitBreederRequest;
use App\Jobs\CreateRabbitBreederJob;
use App\Jobs\UpdateRabbitBreederImageJob;
use App\Jobs\UpdateRabbitBreederJob;
use App\Models\Filters\RabbitBreedersFilter;
use App\Models\Ledger\Category;
use App\Models\Ledger\Entry as LedgerEntry;
use App\Models\Ledger\Entry;
use App\Models\Ledger\Sources\RabbitBreederSold;
use App\Models\RabbitBreeder;
use App\Repositories\RabbitBreederRepository;
use Carbon\Carbon;
use Collective\Bus\Dispatcher;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\BaseIntEncoder;
use Auth;
use Log;

class AdminRabbitBreedersController extends Controller
{
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
        $this->middleware('protect', ['only' => [
            'update', 'updateImage', 'destroy', 'archive', 'show', 'getLitters', 'events', 'sold', 'transfer',
        ]]);
    }

    public function index(RabbitBreedersFilter $filter, Requests\GetRabbitBreedersRequest $request)
    {
        $breeders = $filter->filter(auth()->user()->breeders()->with('user'), 'allBreeders', getenv('BREEDERS_PER_PAGE'));

        return response()->json(['breeders' => $breeders, 'order' => $request->get('order') . '|' . $request->get('orderDirection')]);
    }

    public function show(RabbitBreeder $breeder)
    {
		if($breeder->father()->count()>0)
		{
			$breeder->load('father');
		}
		else
		{
			$breeder->load(['pedigreeFather' => function ($query) {
				$query->where('level','g2.f1')->first();
			}]);
		}
		//dd($breeder->mother()->get()->toArray());
		if($breeder->mother()->count()>0)
		{
			//dd($breeder->has('mother')->get()->toArray());
			$breeder->load('mother');
		}
		else
		{
			$breeder->load(['pedigreeMother' => function ($query) {
				$query->where('level','g2.m1')->first();
			}]);
		}

        //$breeder->load('father', 'mother','user');
		$breeder->load('user');
		//echo '<pre>';
		//print_r($breeder->toArray()); die;
        return $breeder;
    }

    public function store(CreateRabbitBreederRequest $request)
    {
        return $this->dispatcher->dispatchFrom(CreateRabbitBreederJob::class, $request);
    }

    public function update(RabbitBreeder $breeder, UpdateRabbitBreederRequest $request)
    {
        $request['breeder'] = $breeder;

        $updatedBreeder = $this->dispatcher->dispatchFrom(UpdateRabbitBreederJob::class, $request);
        $updatedBreeder->load('mother', 'father');

        return $updatedBreeder;
    }

    public function updateImage(RabbitBreeder $breeder, UpdateRabbitBreederRequest $request)
    {
        $request['breeder'] = $breeder;
        $updatedBreeder = $this->dispatcher->dispatchFrom(UpdateRabbitBreederImageJob::class, $request);

        return $updatedBreeder;
    }

    public function destroy(RabbitBreeder $breeder, RabbitBreederRepository $breeders)
    {
        $breeders->delete($breeder);
    }

    public function butch(RabbitBreeder $breeder, Requests\BreederButchRequest $request)
    {
        $rBreeder = $request->get('breeder');
        if($request->get('butchered')){
            $breeder->weight = $rBreeder['weight'];
            $breeder->butchered = 1;
            $breeder->butchered_at = $request->get('date')
                                        ? Carbon::createFromFormat(\Auth::user()->getDateFormatPHP(), $request->get('date'))
                                        : null;
            $breeder->update();
            event(new RabbitBreederWasButched($breeder));
        } else {
            $breeder->butchered = 0;
            $breeder->butchered_at = null;
            $breeder->update();
            event(new RabbitBreederWasUnbutched($breeder));
        }

        return response()->json([]);
    }

    public function died(RabbitBreeder $breeder, Requests\BreederButchRequest $request)
    {
        if($request->get('died')){
            $breeder->died = 1;
            $breeder->died_at = $request->get('date')
                                    ? Carbon::createFromFormat(\Auth::user()->getDateFormatPHP(), $request->get('date'))
                                    : Carbon::now();
            $breeder->update();
            //event(new RabbitBreederWasDied($breeder));
        } else {
            $breeder->died = 0;
            $breeder->died = null;
            $breeder->death_reason = null;
            $breeder->update();
            //event(new RabbitBreederWasUnDied($breeder));
        }

        return response()->json([]);
    }

    public function archive(RabbitBreeder $breeder, ArchiveRequest $request)
    {
        $breeder->archived = $request->get('archived');
        $breeder->update();
        event(new RabbitBreederWasArchived($breeder));
        return response()->json([]);
    }

    public function sold(RabbitBreeder $breeder, SoldRequest $request)
    {
        $breeder->sold_at = $request->get('sold') ? Carbon::now() : null;
        $breeder->update();
        event(new RabbitBreederWasSold($breeder));

        $ledgerSource = $breeder->soldLedgerSource();
        if ($request->get('sold')) {
            if ($value = $request->get('value', 0)) {
                $ledgerSource->save($value);
            }
        } else {
            $ledgerSource->delete();
        }

        return response()->json([]);
    }

    public function transfer(RabbitBreeder $breeder, TransferRequest $request)
    {
        $breeder->initiateTransfer($request->get('email'));
        if ($entry = $breeder->soldLedgerSource()->find()) {
            $entry->description = 'Sold to ' . $request->get('email');
            $entry->save();
        }
        return response()->json([]);
    }

    public function getList()
    {
        $breeders = auth()->user()->breeders()->select(['id', 'name', 'archived', 'butchered', 'died', 'sold_at', 'tattoo', 'cage', 'color', 'sex', 'breed', 'user_id'])->get();
        $males    = $breeders->where('sex', 'buck')->flatten();
        $females  = $breeders->where('sex', 'doe')->flatten();

        return response()->json([
            'bucks' => $males,
            'does'  => $females,
        ], 200);
    }

    public function getLitters(RabbitBreeder $breeder)
    {
        $litters = $breeder->litters()->with("parents")->orderBy('archived', 'ASC')->orderBy('born', 'DESC')->paginate(getenv('LITTERS_PER_PAGE'));
        foreach($litters as $litter) {
            $father = $litter->parents()->where('sex', '=', 'buck')->first();
            $litter->father_id = $father? $father->id : null;
            $mother = $litter->parents()->where('sex', '=', 'doe')->first();
            $litter->mother_id = $mother? $mother->id : null;
        }
        return $litters;
    }

    public function getPedigree(RabbitBreeder $breeder)
    {
        return response()->json($breeder->pedigree());
    }

    public function checkId(Request $request)
    {
        $count = auth()->user()->breeders()->where('tattoo', '=', $request->get('id'))->count();
        return response()->json(['idDoubled' => (boolean)$count], 200);
    }

    public function checkLimit(Guard $auth)
    {
        $user = $auth->user();
        /* @var $user \App\Models\User */
        $limit = $user->getMaxBreeders();
        return response()->json([
            'ok' => !isset($limit) || $user->breeders()->active(true)->count() < $limit,
        ]);
    }

    public function checkArchivedLimit(Guard $auth)
    {
        $user = $auth->user();
        /* @var $user \App\Models\User */
        $limit = $user->getMaxArchivedBreeders();
        return response()->json([
            'ok' => !isset($limit) || $user->breeders()->active(false)->count() < $limit,
        ]);
    }

    public function events(RabbitBreeder $breeder, Request $request)
    {
        if ($request->has('weekStart')) {
            $date = Carbon::createFromFormat(\Auth::user()->getDateFormatPHP(), $request->get('weekStart'));
            return $breeder->dateWeeklyEvents($date)->get();
        }
        return $breeder->events;
    }

    public function getPdf(Request $request)
    {
        $breeder = RabbitBreeder::find($request->id);
        $pedigree = $breeder->pedigree();
        $directory = public_path() . DIRECTORY_SEPARATOR;
        $owner = $breeder->user;
        if (!$request->query->has('alt')) {
            // Default is mPDF now
            $pdf = \LMPDF::loadView('layouts.profile.pdf', compact('pedigree', 'directory', 'owner'), 'A4-L');
        } else {
            $pdf = \PDF::loadView('layouts.profile.pdf', compact('pedigree', 'owner'), compact('directory'));
        }
        return $pdf->download('Pedigree_' . date('Y_m_d_H_i_s') . '.pdf');

    }

    public function getPdfHtml(Request $request)
    {
        $breeder = RabbitBreeder::find($request->id);
        $pedigree = $breeder->pedigree();
        $directory = public_path() . DIRECTORY_SEPARATOR;
        $owner = $breeder->user;
        return view('layouts.profile.pdf', compact('pedigree', 'directory', 'owner'));
    }

    public function butcherValue(RabbitBreeder $breeders, Request $request, Guard $auth)
    {
        $entry = new Entry();
        $entry->name = 'Butcher: ' . $breeders->name;
        $entry->date = Carbon::now()->format($auth->user()->getDateFormatPHP());
        $entry->category()->associate(Category::where('special', 'breeder')->first());
        $entry->user()->associate($auth->user());
        $entry->debit = true;
        $entry->amount = $request->get('value');
        $breeders->ledgerEntries()->save($entry);
    }

    public function deathReasonValue(RabbitBreeder $breeder, Request $request, Guard $auth)
    {
        if( $request->get('value') ) {
            $breeder->death_reason = $request->get('value');
        }
        $breeder->save();
    }

    public function saveBreeders(Request $request)
    {
        \DB::beginTransaction();
        $fields = $request->get('fields');
        $emptyBreeder = $request->get('emptyBreeder');
        $breedersArr = $request->get('breeders');
        $breedersArr = array_reverse($breedersArr);
        foreach ($breedersArr as $breederData) {
            if($breederData['name']){
            	if(!empty($breederData['status']))
            	{
            		$status = $breederData['status'];
            		if($status == 'archived')
            		{
            			$breederData['archived'] = 1;
            		}
            		elseif($status == 'died')
            		{
            			$breederData['died'] = 1;
            		}
            		elseif ($status == 'butchered')
            		{
            			$breederData['butchered'] = 1;
            		}
            		if(!empty($breederData['status_date']))
            		{
            			$statusDate = $breederData['status_date'];
            			if($status == 'died' || $status == 'butchered' || $status == 'sold')
            			{
            				try
            				{
            					$carbonStatusDate = Carbon::createFromFormat('m/d/Y', $statusDate);
            				}
            				catch(Exception $e)
            				{
            					$carbonStatusDate = Carbon::createFromFormat('d/m/Y', $statusDate);
            				}
            				if($status == 'died')
            				{
            					$breederData['died_at'] = $carbonStatusDate;
            				}
            				elseif($status == 'butchered')
            				{
            					$breederData['butchered_at'] = $carbonStatusDate;
            				}
            				elseif($status == 'sold')
            				{
            					$breederData['sold_at'] = $carbonStatusDate;
            				}
            			}
            			unset($breederData['status_date']);
            		}
            		unset($breederData['status']);
            	}
                array_walk($breederData, function(&$val, $key) use ($fields, $emptyBreeder){
                	if($key != 'father_name' && $key != 'mother_name')
                	{
                    	$val =  $fields[$key]? $val : $emptyBreeder[$key];
                	}
                	else
                	{
                		return $val;
                	}
                });
                $this->dispatcher->dispatchFromArray(CreateRabbitBreederJob::class, $breederData);
            }
        }
        \DB::commit();
        return response()->json([]);
    }

    public function fetchBreeders()
    {
        $authUser = Auth::user();
        $rabbits = RabbitBreeder::where('user_id', $authUser->id)->select('id', 'name')->get();
        return response()->json($rabbits->toArray());
    }

    public function fetchBreeder($id)
    {
        $authUser = Auth::user();
        $rabbit = RabbitBreeder::where('id',$id)->select('prefix','name', 'tattoo', 'color', 'sex', 'breed', 'date_of_birth', 'aquired', 'weight', 'registration_number', 'champion_number', 'legs', 'notes')->first();
        return response()->json($rabbit->toArray());
    }
}
