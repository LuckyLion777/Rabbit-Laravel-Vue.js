<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\UpdateBreedPlanJob;
use App\Jobs\MissedBreedPlanJob;
use App\Models\BreedPlan;
use App\Models\Litter;
use App\Models\RabbitBreeder;
use App\Models\RabbitKit;
use Carbon\Carbon;
use Collective\Bus\Dispatcher;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Http\Requests\MakeBreedPlanRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Jobs\CreateBreedPlanJob;
use Illuminate\Support\Facades\DB;

class AdminReportsController extends Controller
{
    public function statistics(Request $request, Guard $auth)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        if ($from == null) {
            $from = '0001-01-01';
        }
        if ($to == null) {
            $to = '9999-12-31';
        }
        $statistics = $auth->user()->getLedgerStatistics(!!$request->get('archived'), $from, $to);
        $statistics['activeBreedersTotal'] = $auth->user()->breeders()->where('archived', 0)->where('died', 0)->where('butchered', 0)->where('sold_at', null)->count()? : 0;
        $statistics['archivedBreedersTotal'] = $auth->user()->breeders()->where('sold_at', null)->where(function( $query ){
            $query->orWhere('archived', 1)->orWhere('died', 1)->orWhere('butchered', 1);
        })->count()? : 0;

        $statistics['littersTotal'] = $auth->user()->litters()->whereBetween('born', [$from, $to])->count()? : 0;
        $statistics['butcheredLittersTotal'] = $auth->user()->litters()->where('butchered', '1')->whereBetween('butchered_at', [$from, $to])->count()? : 0;
        $statistics['kitsTotal'] = $auth->user()->rabbitKits()
            ->with('litter')
            ->whereHas('litter', function ($query) use ($auth, $from, $to) {  //some litters don't have user_id but their kits do
                $query->where('user_id', $auth->user()->id)->whereBetween('born', [$from, $to]);
            })->count() ? : 0;
        $statistics['diedKitsTotal'] = $auth->user()->diedKits()->with('litter')->whereHas('litter', function ($query) use ($auth, $from, $to) {
            $query->where('user_id', $auth->user()->id)->whereBetween('born', [$from, $to]);
        })->count() ? : 0;
        $statistics['survivalRate'] = 100 - round(($statistics['diedKitsTotal'])*100/($statistics['kitsTotal']?:1), 1);
        $statistics['butcheredKitsTotal'] = $auth->user()->rabbitKits()->butchered()->whereIn('litter_id', auth()->user()->litters()->whereBetween('born', [$from, $to])->pluck('id'))->count()? : 0;

        $butcheredLitters = $auth->user()->litters()->whereNotNull('butchered_at')->whereBetween('butchered_at', [$from, $to])->with(['totalKits' => function ($query) {
            $query->butchered();
        }])->get();

        $butchDays = 0;
        $butchWeight = 0;
        $kitsAmount = 0;
        foreach($butcheredLitters as $litter) {
            $born = Carbon::createFromFormat($auth->user()->getDateFormatPHP(), $litter->born);
            $butchDays += $born->diffInDays($litter->butchered_at);
            $butchWeight += $litter->totalKits->sum('current_weight');
            $kitsAmount += count($litter->totalKits);
        }
        if($butchDays && count($butcheredLitters)){
            $statistics['butchAverageDays'] = round($butchDays / count($butcheredLitters));
            $statistics['butchAverageWeeks'] = round($statistics['butchAverageDays'] / 7);
        } else {
            $statistics['butchAverageDays'] = '-';
            $statistics['butchAverageWeeks'] = '-';
        }
        if($butchWeight && $kitsAmount){
            $precision = 0;
            $averageWeight = $butchWeight / $kitsAmount;
            if($averageWeight < 10){
                $precision = 1;
            }
            $statistics['butchAverageWeight'] = round($averageWeight, $precision);
        } else {
            $statistics['butchAverageWeight'] = '-';
        }

        $statistics['totalMeat'] = 0;

        $statistics['reasonsForDeath'] = $auth->user()->getReasonForDeathStatistics($from, $to);

        //return response()->json($auth->user()->getReasonForDeathStatistics());

        return response()->json(['statistics' => $statistics, 'weightSlug' => $auth->user()->weightSlug]);
    }

    public function does(Request $request, Guard $auth)
    {
        // $to = Carbon::now()->endOfDay();
        // $from = Carbon::now()->subYears(10)->startOfDay();

        $from = $request->get('from');
        $to = $request->get('to');
        if ($from == null) {
            $from = '0001-01-01';
        }
        if ($to == null) {
            $to = '9999-12-31';
        }

        $topWeightDoes = $auth->user()->breeders()->where('sex', 'doe')->where('rabbit_breeders.archived', 0)->where('rabbit_breeders.sold_at', null)
//            ->whereHas('litters', function($query) use ($to, $from){
//                $query->whereBetween('born', [$from, $to]);
//            })
            ->with(['litters' => function($query) use ($to, $from){
                $query->orderBy('created_at', 'desc');
                $query->with('survivedKits');
                $query->whereBetween('born', [$from, $to]);
            }])

            ->select(['rabbit_breeders.*'])->groupBy('id')
            ->get();

        $topWeightDoes->each(function (RabbitBreeder $doe) {
            $doe->breederTotalWeigh = $doe->litters->sum('total_weight');
        })->sortBy('breederTotalWeigh');

        $butcheredKits = $auth->user()->rabbitKits()->butchered()->whereHas('litter', function($query) use ($from, $to) {
            $query->whereBetween('born', [$from, $to]);
        })->with(['litter', 'litter.mother'])->get();

        $topMeatDoes = [];

        foreach($butcheredKits as $kit){
            if ($kit->litter->mother) {
                $doe = collect($kit->litter->mother)->first();
                if ($doe && $doe->archived == 0 && $doe->sold_at == null ) {
                    if (!isset($topMeatDoes[$doe->id])) {
                        $doe->totalMeat = 0;
                        $topMeatDoes[$doe->id] = $doe;
                    }
                    $topMeatDoes[$doe->id]->totalMeat += $kit->current_weight;
                }
            }
        }
        $topMeatDoes = collect($topMeatDoes)->sortByDesc('totalMeat');

        return response()->json([
            'topWeightDoes' => $topWeightDoes,
            'topMeatDoes' => $topMeatDoes->values()
        ]);
    }

    public function bucks(Request $request, Guard $auth)
    {
        // $to = Carbon::now()->endOfDay();
        // $from = Carbon::now()->subYears(10)->startOfDay();

        $from = $request->get('from');
        $to = $request->get('to');
        if ($from == null) {
            $from = '0001-01-01';
        }
        if ($to == null) {
            $to = '9999-12-31';
        }

        $bucks = $auth->user()->breeders()->where('sex', 'buck')->archived(0)->where('rabbit_breeders.sold_at', null)
            ->with(['litters' => function($query) use ($to, $from){
                $query->orderBy('created_at', 'desc');
                $query->with('survivedKits');
                $query->whereBetween('born', [$from, $to]);
            }])->get();

        return response()->json(compact('bucks'));
    }

    private function calcLbsOunces($weight)
    {
        $tLbs = $weight / 16;
        $tmp = explode(".", $tLbs);
        $txt = $tmp[0] > 0 ? $tmp[0] . " lbs " : '';
        $tmp2 = ((float)(($tLbs - $tmp[0]) * 16));
        $txt .= $tmp2 > 0 ? $tmp2 . ' oz' : '';
        return $txt;
    }

}

