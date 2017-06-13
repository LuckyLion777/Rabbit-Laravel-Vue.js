<?php

namespace App\Http\Controllers\Admin;

use App\Events\KitsWereButched;
use App\Events\KitWasDeleted;
use App\Events\KitWasDied;
use App\Events\KitWasArchived;
use App\Events\KitWasRevived;
use App\Events\KitWasSold;
use App\Http\Requests\CreateRabbitKitRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Requests\UpdateRabbitKitRequest;
use App\Http\Requests\WeighKitRequest;
use App\Jobs\CreateRabbitKitJob;
use App\Jobs\MakeRabbitBreederFromKitJob;
use App\Jobs\UpdateRabbitKitJob;
use App\Jobs\WeightKitJob;
use App\Models\Ledger\Category;
use App\Models\Ledger\Sources\RabbitKitSold;
use App\Models\Litter;
use App\Models\RabbitKit;
use App\Repositories\RabbitKitRepository;
use Carbon\Carbon;
use Collective\Bus\Dispatcher;
use Illuminate\Http\Request;
use App\Http\Requests\ArchiveRequest;
use Illuminate\Contracts\Auth\Guard;
use App\Models\Ledger\Entry as LedgerEntry;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AdminRabbitKitsController extends Controller
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * AdminRabbitKitsController constructor.
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->middleware('protect', ['except' => ['store','butch', 'autocomplete']]);
    }

    public function show(RabbitKit $kits)
    {
        return response()->json($kits);
    }

    public function store(CreateRabbitKitRequest $request)
    {
        return $this->dispatcher->dispatchFrom(CreateRabbitKitJob::class, $request);
    }

    public function update(RabbitKit $kit, UpdateRabbitKitRequest $request)
    {
        $request['kit'] = $kit;

        return $this->dispatcher->dispatchFrom(UpdateRabbitKitJob::class, $request);

    }

    public function weigh(RabbitKit $kit, WeighKitRequest $request)
    {
        $request['kit'] = $kit;
        $this->dispatcher->dispatchFrom(WeightKitJob::class, $request);
    }

    public function died(RabbitKit $kit, Request $request)
    {
        if (!$kit->survived) return;

        if($request->get('default_death_reason')){
            $kit->setDefaultDeathReason();
        }

        $kit->alive    = 0;
        $kit->survived = 0;
        $kit->update();
        event(new KitWasDied($kit, 'rabbit'));
    }

    public function revive(RabbitKit $kit)
    {
        if ($kit->survived) return;

        $kit->alive = 1;
        $kit->survived = 1;
        $kit->death_reason = null;
        $kit->update();
        event(new KitWasRevived($kit, 'rabbit'));
    }

    public function archive(RabbitKit $kit, ArchiveRequest $request)
    {
        $kit->archived = $request->get('archived');
        $kit->update();
        event(new KitWasArchived($kit, 'rabbitkit'));
    }

    public function sold(RabbitKit $kit, Request $request)
    {
        $kit->sold_at = $request->get('sold') ? Carbon::now() : null;
        $kit->update();
        event(new KitWasSold($kit, 'rabbitkit'));

        $ledgerSource = $kit->soldLedgerSource();
        if ($request->get('sold')) {
            if ($value = $request->get('value', 0)) {
                $ledgerSource->save($value);
            }
        } else {
            $ledgerSource->delete();
        }

        return response()->json([]);
    }

    public function transfer(RabbitKit $kit, TransferRequest $request)
    {
        $kit->initiateTransfer($request->get('email'));
        if ($entry = $kit->soldLedgerSource()->find()) {
            $entry->description = 'Sold to ' . $request->get('email');
            $entry->save();
        }
        return response()->json([]);
    }

    public function destroy(RabbitKit $kit)
    {
        $kit->delete();
        event(new KitWasDeleted($kit, 'rabbitkit'));
    }

    public function butch(Request $request, RabbitKitRepository $repo)
    {

        $kitsCollection = collect($request->get('kits'));
        $ids            = $kitsCollection->pluck('id')->toArray();
        if (count($ids)) {
            $kits = $repo->whereIn('id', $ids);

            foreach ($kits as $kit) {
                $kit->alive          = 0;
                $kit->survived       = 1;
                $currentWeight = $kitsCollection->where('id', $kit->id)->first()['current_weight'];
                if($currentWeight){
                    $kit->current_weight = $currentWeight;
                }
                $kit->update();
            }
            $litter = Litter::find($request->get('litter_id'));
            event(new KitsWereButched($litter, 'rabbit', count($ids),$request->get('date')));
        }

    }

    public function makeBreeder(RabbitKit $kit)
    {
        $breeder = \DB::transaction(function () use ($kit) {
            $kit->alive = 0;
            $kit->survived = 1;
            $kit->improved = 1;
            $kit->update();
            $breeder = $this->dispatcher->dispatchFromArray(MakeRabbitBreederFromKitJob::class, $kit->toArray());
            return $breeder;
        });
        return $breeder;
    }

    public function getPedigree(RabbitKit $kits)
    {
        return response()->json($kits->pedigree());
    }

    public function pdf(RabbitKit $kits, Request $request)
    {
        $pedigree = $kits->pedigree();
        $pedigree['g1']->token = $kits->token;
        $directory = public_path() . DIRECTORY_SEPARATOR;
        $owner = $kits->user;
        if (!$request->query->has('alt')) {
            // Default is mPDF now
            $pdf = \LMPDF::loadView('layouts.profile.pdf', compact('pedigree', 'directory', 'owner'), 'A4-L');
        } else {
            $pdf = \PDF::loadView('layouts.profile.pdf', compact('pedigree', 'owner'), compact('directory'));
        }
        return $pdf->download('Pedigree_Report_' . date('Y_m_d_H_i_s') . '.pdf');
    }

    public function autocomplete(Guard $auth)
    {
        $fields = ['color', 'weight'];
        $data = [];
        $breeders = $auth->user()->breeders;
        $kits = $auth->user()->rabbitKits;
        foreach ($fields as $field) {
            $collection = $breeders->pluck($field);
            $collection = $collection->merge($kits->pluck($field));
            $collection = $collection->flatten()->filter(function ($value, $key) {
                return $value != null && $value != '';
            });
            $data[$field] = $collection->unique()->sort()->values();
        }

        return response()->json($data);
    }

    public function deathReasonValue(RabbitKit $kit, Request $request)
    {
        if( $request->get('value') ) {
            $kit->death_reason = $request->get('value');
        }
        $kit->save();
    }
}
