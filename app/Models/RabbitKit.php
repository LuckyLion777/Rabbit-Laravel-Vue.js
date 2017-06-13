<?php

namespace App\Models;


use App\Contracts\Soldable;
use App\Helpers\BaseIntEncoder;
use App\Models\Traits\Soldable\SoldableRabbitKit;
use App\Models\Traits\Transferable\TransferableRabbitKit;
use App\Traits\ArchivableTrait;
use App\Traits\CloudinaryImageAbleTrait;
use App\Traits\PedigreeableTrait;
use App\Traits\WeightableTrait;

/**
 * Class RabbitKit
 *
 * @property Litter litter
 *
 * @package App\Models
 */
class RabbitKit extends Animal implements Soldable
{
    use CloudinaryImageAbleTrait, ArchivableTrait, WeightableTrait, PedigreeableTrait,
        SoldableRabbitKit, TransferableRabbitKit {
            WeightableTrait::getWeightSlugAttribute as getWSA;
            WeightableTrait::getWeightConvertedArrayAttribute as getWCA;
            PedigreeableTrait::newPedigree as NP;
    }
    protected $table    = 'rabbit_kits';
    protected $fillable = ['given_id','archived', 'tattoo', 'litter_id', 'user_id', 'alive', 'survived', 'death_reason'];
    protected $appends  = ['weight_unit', 'weight_unit_short', 'weight_slug_array', 'weight_converted_array', 'token'];

    protected $casts = [ 'archived' => 'int' ];

    protected $imagesFolder = 'kits';

    protected $defaultDeathReason = 'stillborn';

    public function litter()
    {
        return $this->belongsTo(Litter::class)->select(['id', 'born', 'given_id', 'total_weight', 'average_weight', 'survival_rate','kits_amount','kits_died', 'butchered_at']);
    }

    public function getWeightAttribute($weight)
    {
        if ($weight) {
            $units = $this->getWeightUnits();
            $generalWeightUnitLabel = $this->getWeightUnitAttribute();
            $newWeight = [];
            if ($units === null) return $weight;
            // var_dump(json_decode($weight));
            foreach(array_filter(json_decode($weight)) as $w)
            {
                if($generalWeightUnitLabel == 'Ounces'){
                    $newWeight[] = $this->getCalOunces($w);
                }
                elseif($generalWeightUnitLabel == 'Grams'){
                    $newWeight[] = $this->getCalGrams($w);
                }
                elseif($generalWeightUnitLabel == 'Pound/Ounces'){
                    $newWeight[] = $this->getCalPoundOunces($w);
                }
                elseif($generalWeightUnitLabel == 'Pounds'){
                    $newWeight[] = $this->getCalPounds($w);
                }
                elseif($generalWeightUnitLabel == 'Kilograms'){
                    $newWeight[] = $this->getCalKilograms($w);
                }
            }

            return $newWeight;
        }

        return null;
    }

    public function setWeightAttribute($weight)
    {
        if ($weight) {
            $units = $this->getWeightUnits();
            $generalWeightUnitLabel = $this->getWeightUnitAttribute();
            // dd($generalWeightUnitLabel);
            if ($units === null) 
            {
                $this->attributes['weight'] = json_encode(array_filter($weight));
            } else {
                $newWeight = [];
                foreach($weight as $w)
                {
                    if($generalWeightUnitLabel == 'Ounces'){
                        $newWeight[] = $w;
                    }
                    elseif($generalWeightUnitLabel == 'Grams'){
                        $newWeight[] = $this->kitCalGramsFromOunces($w);
                    }
                    elseif($generalWeightUnitLabel == 'Pound/Ounces'){
                        $newWeight[] = $this->kitCalPoundOuncesFromOunces($w);
                    }
                    elseif($generalWeightUnitLabel == 'Pounds'){
                        $newWeight[] = $this->kitCalPoundsFromOunces($w);
                    }
                    elseif($generalWeightUnitLabel == 'Kilograms'){
                        $newWeight[] = $this->kitCalKilogramsFromOunces($w);
                    }
                }
                // dd($newWeight);
                $this->attributes['weight'] = json_encode(array_filter($newWeight));
            }
        } else {
            $this->attributes['weight'] = null;
        }
    }
    

    public function getWeightDateAttribute($weight_date)
    {
        if ($weight_date) {
            return array_filter(json_decode($weight_date));
        }

        return null;
    }

    public function setWeightDateAttribute($weight_date)
    {
        if ($weight_date) {
            $this->attributes['weight_date'] = json_encode(array_filter($weight_date));
        } else {
            $this->attributes['weight_date'] = null;
        }
    }

    public function scopeSold($query, $sold = true)
    {
        if ($sold) {
            return $query->whereNotNull('sold_at');
        } else {
            return $query->whereNull('sold_at');
        }
    }

    public function newWeight($newWeight)
    {
        $weight = $this->weight;
        if ( !$weight) {
            $weight = [$newWeight];
        } else {
            array_push($weight, $newWeight);
        }
        $this->weight         = $weight;
        $this->current_weight = $newWeight;
    }

    public function resetWeight()
    {
        $this->weight         = null;
        $this->current_weight = null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->morphToMany(Event::class, 'eventable');
    }

    public function getWeightUnitAttribute()
    {
        return $this->user->general_weight_units;
    }

    public function getWeightUnitShortAttribute()
    {
        return $this->user->weight_slug;
    }

    public function fathers()
    {
        return $this->litter->parentsFull()->where('sex', 'buck');
    }

    public function getFatherAttribute()
    {
        return $this->fathers->first();
    }

    public function mothers()
    {
        return $this->litter->parentsFull()->where('sex', 'doe');
    }

    public function getMotherAttribute()
    {
        return $this->mothers->first();
    }

    /**
     * Like the pedigree for breeders, only optimized
     * @return array
     */
    public function pedigree() {

        $g = $this->user->pedigree_number_generations ? $this->user->pedigree_number_generations : 2;

        $response = [];

        // iterate over existing records
        foreach($this->pedigrees as $p){
            $p = $this->newPedigree($p->level, $p->sex, $p);
            if ($p->level === 'me') {
                $response['g1'] = $p;
            } else {
                $x = explode(".", $p->level);
                $response[$x[0]][$x[1]] = $p;
            }
        }

        // unlike breeders, kits have there onw record in the pedigree table
        if (!isset($response['g1'])) {
            $response['g1'] = $this->newPedigree('me', $this->sex);
        }

        for ($i = 2; $i <= $g; $i++) {
            for ($j = 1, $n = pow(2, $i - 2); $j <= $n; $j++) {
                $lvl = "g{$i}"; $f = "f{$j}"; $m = "m{$j}";
                if (!isset($response[$lvl][$f])) {
                    $response[$lvl][$f] = $this->newPedigree("{$lvl}.{$f}", 'buck');
                }
                if (!isset($response[$lvl][$m])) {
                    $response[$lvl][$m] = $this->newPedigree("{$lvl}.{$m}", 'doe');
                }
            }
        }

        for ($i = $g + 1; $i <= 4; $i++) {
            unset($response['g' . $i]);
        }

        return $response;
    }

    private function ensureOwnPedigreeRecord($pedigree = false)
    {
        if (!$pedigree) $pedigree = new Pedigree();
        $pedigree->rabbit_kit_id = $this->id;
        $pedigree->level = 'me';
        $pedigree->sex = $this->sex;
        $pedigree->custom_id = $this->given_id;
        $pedigree->day_of_birth = $this->litter->born;
        $pedigree->color = $this->color;
        $pedigree->weight = last((array) $this->weight);
        $pedigree->notes = $this->notes;
        $pedigree->image = $this->image['name'];

        $pedigree->save();
        return $pedigree;
    }

    private function newPedigree($level, $sex, $pedigree = false)
    {
        if ($level === 'me') {
            return $this->ensureOwnPedigreeRecord($pedigree);
        }

        return $this->NP($level, $sex, $pedigree);
    }

    /**
     * Pedigree records
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pedigrees()
    {
        return $this->hasMany(Pedigree::class);
    }

    public function getWeightUnits()
    {
        return $this->user ? $this->user->weight_slug : null;
    }

    public function getWeightSlugAttribute()
    {
        $weight = $this->weight;
        if (!$weight) {
            return null;
        }

        $weight = last($weight);
        return $this->getWSA($weight);
    }

    public function getWeightSlugArrayAttribute()
    {
        $weight = $this->weight;
        if (!$weight) return [];
        if (is_array($weight)) {
            return array_map([$this, 'getWSA'], $weight);
        } else {
            return [$this->getWSA($weight)];
        }
    }

    public function getWeightConvertedArrayAttribute()
    {
        $weight = $this->weight;
        if (!$weight) return [];
        if (is_array($weight)) {
            return array_map([$this, 'getWCA'], $weight);
        } else {
            return [$this->getWCA($weight)];
        }
    }

    public function getTokenAttribute()
    {
        return BaseIntEncoder::encode($this->id);
    }

    public function scopeButchered($query)
    {
        return $query->where('improved', 0)->where('survived', 1)->where('alive', 0);
    }

    public function setDefaultDeathReason()
    {
        $this->death_reason = $this->defaultDeathReason;
    }

    public function getDefaultDeathReason()
    {
        return $this->defaultDeathReason;
    }
}
