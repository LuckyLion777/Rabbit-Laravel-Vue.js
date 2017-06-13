<?php

namespace App\Models;

use App\Models\Ledger\Entry as LedgerEntry;
use App\Traits\ArchivableTrait;
use App\Traits\EventsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Media101\Chronicler\Contracts\ChronicleSubject;
use Media101\Chronicler\Traits\HasChronicler;
use Illuminate\Database\Eloquent\Builder;

class Litter extends Model implements ChronicleSubject
{
    use ArchivableTrait, EventsTrait;
    use HasChronicler {
        getChronicleRecordData as getChronicle;
    }

    protected $table    = 'litters';
    protected $fillable = ['archived'];
    protected $hidden   = ['pivot'];
    protected $appends  = ['weight_unit','weight_unit_short','total_weight_slug'];
    protected $dates = ['butchered_at', 'archived_at'];
//    protected $with = ['rabbitKits'];
    protected $casts = [ 'butchered' => 'int' ];

    public function getChronicleRecordData($limited = false)
    {
        $data = $this->getChronicle($limited);
        unset($data['mother_id'], $data['father_id']);
        if (!$limited) {
            $this->load(['buck', 'doe']);
            $data['father'] = ($father = $this->buck->first()) ? ['id' => $father->getKey()] : null;
            $data['mother'] = ($mother = $this->doe->first()) ? ['id' => $mother->getKey()] : null;
        }
        return $data;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentsFull()
    {
        return $this->morphedByMany(RabbitBreeder::class, 'litterable');
    }

    public function parents()
    {
        return $this->morphedByMany(RabbitBreeder::class, 'litterable')->select(['id', 'name', 'sex', 'image', 'litters_count', 'kits', 'live_kits']);
    }

    public function parentsShort()
    {
        return $this->morphedByMany(RabbitBreeder::class, 'litterable')->select(['id', 'name', 'sex']);
    }

    public function mother()
    {
        return $this->morphedByMany(RabbitBreeder::class, 'litterable')->select(['id', 'name', 'sex', 'archived', 'sold_at'])->where('sex', 'doe');
    }

    public function father()
    {
        return $this->morphedByMany(RabbitBreeder::class, 'litterable')->select(['id', 'name', 'sex', 'archived', 'sold_at'])->where('sex', 'buck');
    }

    public function archivedParents()
    {
        return $this->morphedByMany(RabbitBreeder::class, 'litterable')->where('archived', '=', 1)->select(['id']);
    }

    public function weighs()
    {
        return $this->morphToMany(Event::class, 'eventable')
            ->where('subtype', 'weigh')
            ->where('closed', '=', 1);
    }

    public function setBornAttribute($born)
    {
        if ($born) {
            $this->attributes['born'] = Carbon::createFromFormat(User::getDateFormatPHPSafe(), $born)
                                                ->toDateString();
        } else {
            $this->attributes['born'] = null;
        }
    }

    public function getBornAttribute($born)
    {
        if ($born) {
            return Carbon::createFromFormat('Y-m-d', $born)->format(User::getDateFormatPHPSafe());
        } else {
            return $this->attributes['born'] = null;
        }
    }

    public function getBredAttribute($bred)
    {
        if ($bred) {
            return Carbon::createFromFormat('Y-m-d', $bred)->format(User::getDateFormatPHPSafe());
        } else {
            return $this->attributes['bred'] = null;
        }
    }


    public function setBredAttribute($bred)
    {
        if ($bred) {
            $this->attributes['bred'] = Carbon::createFromFormat(User::getDateFormatPHPSafe(), $bred)
                                                ->toDateString();
        } else {
            $this->attributes['bred'] = null;
        }
    }

    public function scopeButchered($query, $butchered)
    {
        if ($butchered !== null)
            return $query->where('butchered', '=', $butchered);

        return $query;
    }

    public function kitsPaged()
    {
        return collect($this->rabbitKits()->paginate(getenv('KITS_PER_LITTER')));
    }

    public function kits()
    {
        return $this->rabbitKits;
    }

    public function survivedKits()
    {
        return $this->hasMany(RabbitKit::class)->where('survived', '=', 1);
    }

    public function diedKits()
    {
        return $this->hasMany(RabbitKit::class)->where('survived', '=', 0);
    }

    public function kitsCount()
    {
        return $this->rabbitKits()->count();
    }

    public function kitsForButchCount()
    {
        return $this->kitsForButch()->count();
    }

    public function kitsForButch()
    {
        return $this->rabbitKits()->where('sold_at', null)->where('improved', 0)->where('archived', 0);
    }

    public function kitsButchered()
    {
        return $this->hasMany(RabbitKit::class)->where('survived', '=', 1)->where('alive', 0)->where('improved', 0)->where('sold_at', null)->where('archived', 0);
    }

    public function rabbitKits()
    {
        return $this->hasMany(RabbitKit::class)->where('alive', '=', 1);
    }

    public function totalKits()
    {
        return $this->hasMany(RabbitKit::class);
    }

    public function setAge()
    {
        $born = Carbon::createFromFormat(User::getDateFormatPHPSafe(), $this->born);
        $now  = Carbon::now();
        $age  = ($now->diff($born)->days < 7) ? '1 week' : $now->diffForHumans($born, true);

        $this->setAttribute('age', $age);
    }

    public function updateWeights()
    {
        $weights = $this->survivedKits()->where('archived', 0)->lists('current_weight');//TODO change in case of multiple animal

        $this->total_weight   = $weights->sum();
        $this->average_weight = $weights->avg();
    }

    public function getWeightUnitAttribute()
    {
        if ($this->user){
            return $this->user->general_weight_units;
        }
        return null;
    }

    public function getWeightUnitShortAttribute()
    {
        if ($this->user){
            return $this->user->weight_slug;
        }
        return null;
    }

    public function getTotalWeightSlugAttribute()
    {

        if ($this->user){
            if ($this->user->general_weight_units == 'Pound/Ounces'):

                /*
                 *
        kit weights:
        1.2 lb/oz (1 lb 2 oz)
        1.3 lb/oz
        1.5 lb/oz
        1.3 lb/oz

        first, convert to oz:
        1 lb 2 oz = (1x16) + 2 = 18 oz
        1.3 lb/oz = (1x16) + 3 = 19 oz
        1.5 lb/oz = (1x16) + 5 = 21 oz
        1.3 lb/oz = (1x16) + 3 = 19 oz

        total = 77 oz
        average = 77 oz / 4 kits = 19.25 oz

        then, convert to lbs:
        total = 77/16 = 4.8 lbs = 4 lbs + .8 lbs
        Convert .8 lbs to oz: (.8x 16) = 13 oz
        total = 4 lbs 13 oz

        average = 77 oz / 4 kits = 19.25 oz
        19.25/16 = 1.20 lbs = 1 lbs + .2 lbs
        Convert .2 lbs to oz: (.2x 16) = 3.2 (round to whole) = 3 oz
        average = 1 lbs 3 oz
                *
                 */
                //First step
                $count = 0;
                $total = 0;
                foreach ($this->survivedKits()->get() as $k) {
                    $total += ($k->current_weight ?: 0);
                    $count++;
                }

                $average = $count ? $total / $count : 0;

                //Second step in total
                $tLbs = $total / 16;
                $tmp = explode(".", $tLbs);
                $total = $tmp[0] > 0 ? $tmp[0] . " lbs " : '';
                $tmp2 = ((float)(($tLbs - $tmp[0]) * 16));
                $total .= $tmp2 > 0 ? $tmp2 . ' oz' : '';

                //Second step in average
                $tLbs = $average / 16;
                $tmp = explode(".", $tLbs);
                $average = $tmp[0] > 0 ? $tmp[0] . " lbs " : '';
                $tmp2 = round((float)(($tLbs - $tmp[0]) * 16), 2);
                $average .= $tmp2 > 0 ? $tmp2 . ' oz' : '';
            //$average = $tLbs - $tmp[0];

            else:
                $total = $this->total_weight && $this->total_weight > 0 ? $this->total_weight . " " . $this->user->weight_slug : '';
                $average = $this->average_weight && $this->average_weight > 0 ? $this->average_weight . " " . $this->user->weight_slug : '';
            endif;

            return [
                'total' => $total,
                'average' => $average
            ];
        }
        return null;
    }

    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, 'associated');
    }

    public function buck()
    {
        return $this->morphedByMany(RabbitBreeder::class, 'litterable')->select(['id', 'name', 'sex', 'archived', 'sold_at'])->where('sex', 'buck');
    }

    public function doe()
    {
        return $this->morphedByMany(RabbitBreeder::class, 'litterable')->select(['id', 'name', 'sex', 'archived', 'sold_at'])->where('sex', 'doe');
    }
}
