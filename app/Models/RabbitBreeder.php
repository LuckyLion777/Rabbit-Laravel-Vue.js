<?php

namespace App\Models;

use App\Contracts\Purchaseable;
use App\Contracts\Soldable;
use App\Models\BreedPlan;
use App\Models\Ledger\Entry as LedgerEntry;
use App\Models\Traits\Soldable\PurchaseableRabbitBreeder;
use App\Models\Traits\Soldable\SoldableRabbitBreeder;
use App\Models\Traits\Transferable\TransferableRabbitBreeder;
use App\Traits\ArchivableTrait;
use App\Traits\EventsTrait;
use App\Traits\CloudinaryImageAbleTrait;
use App\Traits\PedigreeableTrait;
use App\Traits\WeightableTrait;
use Carbon\Carbon;
use App\Helpers\BaseIntEncoder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RabbitBreeder extends Animal implements Soldable, Purchaseable
{
    use CloudinaryImageAbleTrait, ArchivableTrait, EventsTrait, WeightableTrait, PedigreeableTrait,
        SoldableRabbitBreeder, TransferableRabbitBreeder, PurchaseableRabbitBreeder;

    protected $table = 'rabbit_breeders';
    protected $imagesFolder = 'breeders';
    protected $casts = [
        // Cannot do it, 'cause 0.10 != 0.1
//        'weight' => 'float',
        'archived' => 'int',
        'butchered' => 'int',
        'died' => 'int',
    ];

    protected $appends = [
        'weight_slug',
        'weight_unit',
        'css',
        'token',
        'cat_name',
        'pregnant',
        'misses_count',
        'plan_id'
    ];

    public function getWeightUnitAttribute()
    {
        if($this->user){
            return $this->user->general_weight_units;
        }
    }

    public function getCatNameAttribute()
    {
        return $this->category ? $this->category->name : '';
    }

    public function getPregnantAttribute()
    {
        if ($this->sex !== 'doe') {
            return false;
        }
        $plans = $this->plans()->where('missed_date', null)->get();
        /* @var $plans Collection|BreedPlan[] */
        return !!$plans->first(function ($_, BreedPlan $plan) {
            $breed = $plan->events->first(function ($_, Event $event) {
                return $event->subtype === 'breed';
            });
            if (!$breed || !$breed->closed) {
                // Not pregnant yet
                return false;
            }
            $birth = $plan->events->first(function ($_, Event $event) {
                return $event->subtype === 'birth';
            });
            if (!$birth || $birth->closed) {
                // Not pregnant anymore
                return false;
            }
            return true;
        });
    }
    public function getPlanIdAttribute() {
        $plans = $this->plans()->where('missed_date', null)->get();
        /* @var $plans Collection|BreedPlan[] */
        $sel_plan = $plans->first(function ($_, BreedPlan $plan) {
            $breed = $plan->events->first(function ($_, Event $event) {
                return $event->subtype === 'breed';
            });
            if (!$breed || !$breed->closed) {
                return false;
            }
            $birth = $plan->events->first(function ($_, Event $event) {
                return $event->subtype === 'birth';
            });
            if (!$birth || $birth->closed) {
                return false;
            }
            return $birth->breed_id;
        });
        if ($sel_plan) {
            return $sel_plan->id;
        } else {
            return false;
        }
    }
    public function category()
    {
        return $this->belongsTo(RabbitBreederCategory::class, 'category_id');
    }


    public function setAquiredAttribute($acquired)
    {
        if ($acquired) {
            $this->attributes['aquired'] = Carbon::createFromFormat(User::getDateFormatPHPSafe(), $acquired)
                                                    ->toDateString();
        } else {
            $this->attributes['aquired'] = null;
        }
    }

    public function getAquiredAttribute($acquired)
    {
        if ($acquired) {
            return Carbon::createFromFormat('Y-m-d', $acquired)->format(User::getDateFormatPHPSafe());
        }
        return null;
    }

    public function setDateOfBirthAttribute($date_of_birth)
    {
        if ($date_of_birth) {
            $this->attributes['date_of_birth'] = Carbon::createFromFormat(
                User::getDateFormatPHPSafe(),
                $date_of_birth
            )->toDateString();
        } else {
            $this->attributes['date_of_birth'] = null;
        }
    }

    public function getDateOfBirthAttribute($date_of_birth)
    {
        if ($date_of_birth) {
            return Carbon::createFromFormat('Y-m-d', $date_of_birth)->format(User::getDateFormatPHPSafe());
        }
        return null;
    }

    public function getLittersCountAttribute(){
        return $this->litters()->count();
    }

    public function getKitsAttribute(){
        $litters = $this->litters()->with('rabbitKits')->get();
        $count = 0;
        foreach($litters as $litter){
            $count += $litter->survivedKits()->count();
        }
        if($count){
            return $count;
        }
        return null;
    }

    public function getLiveKitsAttribute(){
        $litters = $this->litters()->where('archived', 0)->get();
        $count = 0;
        foreach($litters as $litter){
            $count += $litter->rabbitKits()->where('archived', 0)->count();
        }
        if($count){
            return $count;
        }
        return null;
    }

    public function kits()
    {
        return $this->hasManyThrough(RabbitKit::class, Litter::class, 'country_id', 'user_id');
    }

    public function father()
    {
        //return $this->hasOne(RabbitBreeder::class, 'id', 'father_id')->select(['id', 'name', 'tattoo']);
        return $this->belongsTo(RabbitBreeder::class, 'father_id', 'id');
    }

    public function mother()
    {
        //return $this->hasOne(RabbitBreeder::class, 'id', 'mother_id')->select(['id', 'name', 'tattoo']);
        return $this->belongsTo(RabbitBreeder::class, 'mother_id', 'id');
    }

	public function pedigreeFather()
    {
        //return $this->hasOne(RabbitBreeder::class, 'id', 'father_id')->select(['id', 'name', 'tattoo']);
        return $this->hasMany('App\Models\Pedigree', 'rabbit_breeder_id', 'id');
    }
	
	public function pedigreeMother()
    {
        //return $this->hasOne(RabbitBreeder::class, 'id', 'father_id')->select(['id', 'name', 'tattoo']);
        return $this->hasMany('App\Models\Pedigree', 'rabbit_breeder_id', 'id');
    }
	
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', '=', auth()->user()->id);
    }

    public function scopeSex($query, $sex)
    {
        if ($sex)
            return $query->where('sex', '=', $sex);

        return $query;
    }

    public function scopeSold($query, $sold = true)
    {
        if ($sold) {
            return $query->whereNotNull('sold_at');
        } else {
            return $query->whereNull('sold_at');
        }
    }

    public function scopeButchered($query, $butchered = true)
    {
        if ($butchered) {
            return $query->where('butchered', 1);
        } else {
            return $query->where('butchered', 0);
        }
    }

    public function scopeDied($query, $died = true)
    {
        if ($died) {
            return $query->where('died', 1);
        } else {
            return $query->where('died', 0);
        }
    }

    public function scopeActive($query, $active = true)
    {
        return $query->where(function (Builder $query) use ($active) {
            if ($active) {
                $query->where('archived', '0')->whereNull('sold_at');
            } else {
                $query->where('archived', '1')->whereNotNull('sold_at', 'or');
            }
        });
    }

    public function plans()
    {
        return $this->morphToMany(BreedPlan::class, 'plannable', 'plannables', null, 'plan_id');
    }

    public function litters()
    {
        return $this->morphToMany(Litter::class, 'litterable');
    }

    public function getWeightUnits()
    {
        return $this->user ? $this->user->weight_slug : null;
    }

    public function getCssAttribute()
    {
        if($this->sex =='buck') {
            return [
                'icon'  =>  "fa fa-mars",
                'color' =>  "bg-aqua",
                'img'   =>  'icon-male.png'
            ];
        }

        return [
        'icon'  =>  "fa fa-venus",
        'color' =>  "bg-maroon",
        'img'   =>  'icon-female.png'
        ];
    }

    public function pedigree(){

        $g = $this->user->pedigree_number_generations ? $this->user->pedigree_number_generations : 2;

        $response = [];
        $response['g1'] = $this->getFirstGen();

        //create tree
        foreach($this->pedigrees as $p){
            if($p->level != 'me') {
                $p = $this->newPedigree($p->level,$p->sex,$p);
                $x = explode(".",$p->level);
                $response[$x[0]][$x[1]] = $p;
            }
        }
        
        $l = 1;
        for($i=2; $i<=$g; $i++)
        {
            for($j=1;$j<=$l;$j++)
            {
                $response['g' . $i]['f'.$j] = !isset($response['g' . $i]['f'.$j]) ? $this->newPedigree('g'. $i.'.f'.$j,'buck') : $this->newPedigree('g'. $i.'.f'.$j,'buck',$response['g'. $i]['f'.$j]);
                $response['g'. $i]['m'.$j] = !isset($response['g'. $i]['m'.$j]) ? $this->newPedigree('g'. $i.'.m'.$j,'doe') : $this->newPedigree('g'. $i.'.m'.$j,'doe', $response['g'. $i]['m'.$j]);
            }
            $l *= 2; 
        }
        
        for($k=$g+1; $k<=7; $k++){
            unset($response['g'.$k]);
        }
        return $response;
		
        //LEVEL II
        /*if($g>=2){

            $response['g2']['f1'] = !isset($response['g2']['f1']) ? $this->newPedigree('g2.f1','buck') : $this->newPedigree('g2.f1','buck',$response['g2']['f1']);
            $response['g2']['m1'] = !isset($response['g2']['m1']) ? $this->newPedigree('g2.m1','doe') : $this->newPedigree('g2.m1','doe', $response['g2']['m1']);
            //if(!isset($response['g2']['f1'])) $response['g2']['f1'] = $this->newPedigree('g2.f1','buck');
            //if(!isset($response['g2']['m1'])) $response['g2']['m1'] = $this->newPedigree('g2.m1','doe');
        }

        //LEVEL III
        if($g>=3){

            $response['g3']['f1'] = !isset($response['g3']['f1']) ? $this->newPedigree('g3.f1','buck') : $this->newPedigree('g3.f1','buck', $response['g3']['f1']);
            $response['g3']['m1'] = !isset($response['g3']['m1']) ? $this->newPedigree('g3.m1','doe') : $this->newPedigree('g3.m1','doe', $response['g3']['m1']);

            $response['g3']['f2'] = !isset($response['g3']['f2']) ? $this->newPedigree('g3.f2','buck'): $this->newPedigree('g3.f2','buck', $response['g3']['f2']);
            $response['g3']['m2'] = !isset($response['g3']['m2']) ? $this->newPedigree('g3.m2','doe') : $this->newPedigree('g3.m2','doe', $response['g3']['m2']);

            //if(!isset($response['g3']['f1'])) $response['g3']['f1'] = $this->newPedigree('g3.f1','buck');
            //if(!isset($response['g3']['m1'])) $response['g3']['m1'] = $this->newPedigree('g3.m1','doe');

            //if(!isset($response['g3']['f2'])) $response['g3']['f2'] = $this->newPedigree('g3.f2','buck');
            //if(!isset($response['g3']['m2'])) $response['g3']['m2'] = $this->newPedigree('g3.m2','doe');
        }

        //LEVEL III
        if($g>=4){

            $response['g4']['f1'] = !isset($response['g4']['f1']) ? $this->newPedigree('g4.f1','buck') : $this->newPedigree('g4.f1','buck', $response['g4']['f1']);
            $response['g4']['m1'] = !isset($response['g4']['m1']) ? $this->newPedigree('g4.m1','doe') : $this->newPedigree('g4.m1','doe', $response['g4']['m1']);

            $response['g4']['f2'] = !isset($response['g4']['f2']) ? $this->newPedigree('g4.f2','buck') : $this->newPedigree('g4.f2','buck',$response['g4']['f2']);
            $response['g4']['m2'] = !isset($response['g4']['m2']) ? $this->newPedigree('g4.m2','doe') : $this->newPedigree('g4.m2','doe', $response['g4']['m2']);

            $response['g4']['f3'] = !isset($response['g4']['f3']) ? $this->newPedigree('g4.f3','buck') : $this->newPedigree('g4.f3','buck', $response['g4']['f3']);
            $response['g4']['m3'] = !isset($response['g4']['m3']) ? $this->newPedigree('g4.m3','doe') : $this->newPedigree('g4.m3','doe', $response['g4']['m3']);

            $response['g4']['f4'] = !isset($response['g4']['f4']) ? $this->newPedigree('g4.f4','buck') : $this->newPedigree('g4.f4','buck', $response['g4']['f4']);
            $response['g4']['m4'] = !isset($response['g4']['m4']) ? $this->newPedigree('g4.m4','doe') : $this->newPedigree('g4.m4','doe', $response['g4']['m4']);




            //if(!isset($response['g4']['f1'])) $response['g4']['f1'] = $this->newPedigree('g4.f1','buck');
            //if(!isset($response['g4']['m1'])) $response['g4']['m1'] = $this->newPedigree('g4.m1','doe');

            //if(!isset($response['g4']['f2'])) $response['g4']['f2'] = $this->newPedigree('g4.f2','buck');
            //if(!isset($response['g4']['m2'])) $response['g4']['m2'] = $this->newPedigree('g4.m2','doe');

            //if(!isset($response['g4']['f3'])) $response['g4']['f3'] = $this->newPedigree('g4.f3','buck');
            //if(!isset($response['g4']['m3'])) $response['g4']['m3'] = $this->newPedigree('g4.m3','doe');

            //if(!isset($response['g4']['f4'])) $response['g4']['f4'] = $this->newPedigree('g4.f4','buck');
            //if(!isset($response['g4']['m4'])) $response['g4']['m4'] = $this->newPedigree('g4.m4','doe');
        }

		
		if($g>=5){

            $response['g5']['f1'] = !isset($response['g5']['f1']) ? $this->newPedigree('g5.f1','buck') : $this->newPedigree('g5.f1','buck', $response['g5']['f1']);
            $response['g5']['m1'] = !isset($response['g5']['m1']) ? $this->newPedigree('g5.m1','doe') : $this->newPedigree('g5.m1','doe', $response['g5']['m1']);

            $response['g5']['f2'] = !isset($response['g5']['f2']) ? $this->newPedigree('g5.f2','buck') : $this->newPedigree('g5.f2','buck',$response['g5']['f2']);
            $response['g5']['m2'] = !isset($response['g5']['m2']) ? $this->newPedigree('g5.m2','doe') : $this->newPedigree('g5.m2','doe', $response['g5']['m2']);

            $response['g5']['f3'] = !isset($response['g5']['f3']) ? $this->newPedigree('g5.f3','buck') : $this->newPedigree('g5.f3','buck', $response['g5']['f3']);
            $response['g5']['m3'] = !isset($response['g5']['m3']) ? $this->newPedigree('g5.m3','doe') : $this->newPedigree('g5.m3','doe', $response['g5']['m3']);

            $response['g5']['f4'] = !isset($response['g5']['f4']) ? $this->newPedigree('g5.f4','buck') : $this->newPedigree('g5.f4','buck', $response['g5']['f4']);
            $response['g5']['m4'] = !isset($response['g5']['m4']) ? $this->newPedigree('g5.m4','doe') : $this->newPedigree('g5.m4','doe', $response['g5']['m4']);
			
			$response['g5']['f5'] = !isset($response['g5']['f5']) ? $this->newPedigree('g5.f5','buck') : $this->newPedigree('g5.f5','buck', $response['g5']['f5']);
            $response['g5']['m5'] = !isset($response['g5']['m5']) ? $this->newPedigree('g5.m5','doe') : $this->newPedigree('g5.m5','doe', $response['g5']['m5']);

            $response['g5']['f6'] = !isset($response['g5']['f6']) ? $this->newPedigree('g5.f6','buck') : $this->newPedigree('g5.f6','buck',$response['g5']['f6']);
            $response['g5']['m6'] = !isset($response['g5']['m6']) ? $this->newPedigree('g5.m6','doe') : $this->newPedigree('g5.m6','doe', $response['g5']['m6']);

            $response['g5']['f7'] = !isset($response['g5']['f7']) ? $this->newPedigree('g5.f7','buck') : $this->newPedigree('g5.f7','buck', $response['g5']['f7']);
            $response['g5']['m7'] = !isset($response['g5']['m7']) ? $this->newPedigree('g5.m7','doe') : $this->newPedigree('g5.m7','doe', $response['g5']['m7']);

            $response['g5']['f8'] = !isset($response['g5']['f8']) ? $this->newPedigree('g5.f8','buck') : $this->newPedigree('g5.f8','buck', $response['g5']['f8']);
            $response['g5']['m8'] = !isset($response['g5']['m8']) ? $this->newPedigree('g5.m8','doe') : $this->newPedigree('g5.m8','doe', $response['g5']['m8']);

        }

		if($g>=6){

            $response['g6']['f1'] = !isset($response['g6']['f1']) ? $this->newPedigree('g6.f1','buck') : $this->newPedigree('g6.f1','buck', $response['g6']['f1']);
            $response['g6']['m1'] = !isset($response['g6']['m1']) ? $this->newPedigree('g6.m1','doe') : $this->newPedigree('g6.m1','doe', $response['g6']['m1']);

            $response['g6']['f2'] = !isset($response['g6']['f2']) ? $this->newPedigree('g6.f2','buck') : $this->newPedigree('g6.f2','buck',$response['g6']['f2']);
            $response['g6']['m2'] = !isset($response['g6']['m2']) ? $this->newPedigree('g6.m2','doe') : $this->newPedigree('g6.m2','doe', $response['g6']['m2']);

            $response['g6']['f3'] = !isset($response['g6']['f3']) ? $this->newPedigree('g6.f3','buck') : $this->newPedigree('g6.f3','buck', $response['g6']['f3']);
            $response['g6']['m3'] = !isset($response['g6']['m3']) ? $this->newPedigree('g6.m3','doe') : $this->newPedigree('g6.m3','doe', $response['g6']['m3']);

            $response['g6']['f4'] = !isset($response['g6']['f4']) ? $this->newPedigree('g6.f4','buck') : $this->newPedigree('g6.f4','buck', $response['g6']['f4']);
            $response['g6']['m4'] = !isset($response['g6']['m4']) ? $this->newPedigree('g6.m4','doe') : $this->newPedigree('g6.m4','doe', $response['g6']['m4']);
			
			$response['g6']['f5'] = !isset($response['g6']['f5']) ? $this->newPedigree('g6.f5','buck') : $this->newPedigree('g6.f5','buck', $response['g6']['f5']);
            $response['g6']['m5'] = !isset($response['g6']['m5']) ? $this->newPedigree('g6.m5','doe') : $this->newPedigree('g6.m5','doe', $response['g6']['m5']);

            $response['g6']['f6'] = !isset($response['g6']['f6']) ? $this->newPedigree('g6.f6','buck') : $this->newPedigree('g6.f6','buck',$response['g6']['f6']);
            $response['g6']['m6'] = !isset($response['g6']['m6']) ? $this->newPedigree('g6.m6','doe') : $this->newPedigree('g6.m6','doe', $response['g6']['m6']);

            $response['g6']['f7'] = !isset($response['g6']['f7']) ? $this->newPedigree('g6.f7','buck') : $this->newPedigree('g6.f7','buck', $response['g6']['f7']);
            $response['g6']['m7'] = !isset($response['g6']['m7']) ? $this->newPedigree('g6.m7','doe') : $this->newPedigree('g6.m7','doe', $response['g6']['m7']);

            $response['g6']['f8'] = !isset($response['g6']['f8']) ? $this->newPedigree('g6.f8','buck') : $this->newPedigree('g6.f8','buck', $response['g6']['f8']);
            $response['g6']['m8'] = !isset($response['g6']['m8']) ? $this->newPedigree('g6.m8','doe') : $this->newPedigree('g6.m8','doe', $response['g6']['m8']);
			
			$response['g6']['f9'] = !isset($response['g6']['f9']) ? $this->newPedigree('g6.f9','buck') : $this->newPedigree('g6.f9','buck', $response['g6']['f9']);
            $response['g6']['m9'] = !isset($response['g6']['m9']) ? $this->newPedigree('g6.m9','doe') : $this->newPedigree('g6.m9','doe', $response['g6']['m9']);

            $response['g6']['f10'] = !isset($response['g6']['f10']) ? $this->newPedigree('g6.f10','buck') : $this->newPedigree('g6.f10','buck',$response['g6']['f10']);
            $response['g6']['m10'] = !isset($response['g6']['m10']) ? $this->newPedigree('g6.m10','doe') : $this->newPedigree('g6.m10','doe', $response['g6']['m10']);

            $response['g6']['f11'] = !isset($response['g6']['f11']) ? $this->newPedigree('g6.f11','buck') : $this->newPedigree('g6.f11','buck', $response['g6']['f11']);
            $response['g6']['m11'] = !isset($response['g6']['m11']) ? $this->newPedigree('g6.m11','doe') : $this->newPedigree('g6.m11','doe', $response['g6']['m11']);

            $response['g6']['f12'] = !isset($response['g6']['f12']) ? $this->newPedigree('g6.f12','buck') : $this->newPedigree('g6.f12','buck', $response['g6']['f12']);
            $response['g6']['m12'] = !isset($response['g6']['m12']) ? $this->newPedigree('g6.m12','doe') : $this->newPedigree('g6.m12','doe', $response['g6']['m12']);
			
			$response['g6']['f13'] = !isset($response['g6']['f13']) ? $this->newPedigree('g6.f13','buck') : $this->newPedigree('g6.f13','buck', $response['g6']['f13']);
            $response['g6']['m13'] = !isset($response['g6']['m13']) ? $this->newPedigree('g6.m13','doe') : $this->newPedigree('g6.m13','doe', $response['g6']['m13']);

            $response['g6']['f14'] = !isset($response['g6']['f14']) ? $this->newPedigree('g6.f14','buck') : $this->newPedigree('g6.f14','buck',$response['g6']['f14']);
            $response['g6']['m14'] = !isset($response['g6']['m14']) ? $this->newPedigree('g6.m14','doe') : $this->newPedigree('g6.m14','doe', $response['g6']['m14']);

            $response['g6']['f15'] = !isset($response['g6']['f15']) ? $this->newPedigree('g6.f15','buck') : $this->newPedigree('g6.f15','buck', $response['g6']['f15']);
            $response['g6']['m15'] = !isset($response['g6']['m15']) ? $this->newPedigree('g6.m15','doe') : $this->newPedigree('g6.m15','doe', $response['g6']['m15']);

            $response['g6']['f16'] = !isset($response['g6']['f16']) ? $this->newPedigree('g6.f16','buck') : $this->newPedigree('g6.f16','buck', $response['g6']['f16']);
            $response['g6']['m16'] = !isset($response['g6']['m16']) ? $this->newPedigree('g6.m16','doe') : $this->newPedigree('g6.m16','doe', $response['g6']['m16']);

        }
		
		if($g>=7){

            $response['g7']['f1'] = !isset($response['g7']['f1']) ? $this->newPedigree('g7.f1','buck') : $this->newPedigree('g7.f1','buck', $response['g7']['f1']);
            $response['g7']['m1'] = !isset($response['g7']['m1']) ? $this->newPedigree('g7.m1','doe') : $this->newPedigree('g7.m1','doe', $response['g7']['m1']);

            $response['g7']['f2'] = !isset($response['g7']['f2']) ? $this->newPedigree('g7.f2','buck') : $this->newPedigree('g7.f2','buck',$response['g7']['f2']);
            $response['g7']['m2'] = !isset($response['g7']['m2']) ? $this->newPedigree('g7.m2','doe') : $this->newPedigree('g7.m2','doe', $response['g7']['m2']);

            $response['g7']['f3'] = !isset($response['g7']['f3']) ? $this->newPedigree('g7.f3','buck') : $this->newPedigree('g7.f3','buck', $response['g7']['f3']);
            $response['g7']['m3'] = !isset($response['g7']['m3']) ? $this->newPedigree('g7.m3','doe') : $this->newPedigree('g7.m3','doe', $response['g7']['m3']);

            $response['g7']['f4'] = !isset($response['g7']['f4']) ? $this->newPedigree('g7.f4','buck') : $this->newPedigree('g7.f4','buck', $response['g7']['f4']);
            $response['g7']['m4'] = !isset($response['g7']['m4']) ? $this->newPedigree('g7.m4','doe') : $this->newPedigree('g7.m4','doe', $response['g7']['m4']);
			
			$response['g7']['f5'] = !isset($response['g7']['f5']) ? $this->newPedigree('g7.f5','buck') : $this->newPedigree('g7.f5','buck', $response['g7']['f5']);
            $response['g7']['m5'] = !isset($response['g7']['m5']) ? $this->newPedigree('g7.m5','doe') : $this->newPedigree('g7.m5','doe', $response['g7']['m5']);

            $response['g7']['f6'] = !isset($response['g7']['f6']) ? $this->newPedigree('g7.f6','buck') : $this->newPedigree('g7.f6','buck',$response['g7']['f6']);
            $response['g7']['m6'] = !isset($response['g7']['m6']) ? $this->newPedigree('g7.m6','doe') : $this->newPedigree('g7.m6','doe', $response['g7']['m6']);

            $response['g7']['f7'] = !isset($response['g7']['f7']) ? $this->newPedigree('g7.f7','buck') : $this->newPedigree('g7.f7','buck', $response['g7']['f7']);
            $response['g7']['m7'] = !isset($response['g7']['m7']) ? $this->newPedigree('g7.m7','doe') : $this->newPedigree('g7.m7','doe', $response['g7']['m7']);

            $response['g7']['f8'] = !isset($response['g7']['f8']) ? $this->newPedigree('g7.f8','buck') : $this->newPedigree('g7.f8','buck', $response['g7']['f8']);
            $response['g7']['m8'] = !isset($response['g7']['m8']) ? $this->newPedigree('g7.m8','doe') : $this->newPedigree('g7.m8','doe', $response['g7']['m8']);
			
			$response['g7']['f9'] = !isset($response['g7']['f9']) ? $this->newPedigree('g7.f9','buck') : $this->newPedigree('g7.f9','buck', $response['g7']['f9']);
            $response['g7']['m9'] = !isset($response['g7']['m9']) ? $this->newPedigree('g7.m9','doe') : $this->newPedigree('g7.m9','doe', $response['g7']['m9']);

            $response['g7']['f10'] = !isset($response['g7']['f10']) ? $this->newPedigree('g7.f10','buck') : $this->newPedigree('g7.f10','buck',$response['g7']['f10']);
            $response['g7']['m10'] = !isset($response['g7']['m10']) ? $this->newPedigree('g7.m10','doe') : $this->newPedigree('g7.m10','doe', $response['g7']['m10']);

            $response['g7']['f11'] = !isset($response['g7']['f11']) ? $this->newPedigree('g7.f11','buck') : $this->newPedigree('g7.f11','buck', $response['g7']['f11']);
            $response['g7']['m11'] = !isset($response['g7']['m11']) ? $this->newPedigree('g7.m11','doe') : $this->newPedigree('g7.m11','doe', $response['g7']['m11']);

            $response['g7']['f12'] = !isset($response['g7']['f12']) ? $this->newPedigree('g7.f12','buck') : $this->newPedigree('g7.f12','buck', $response['g7']['f12']);
            $response['g7']['m12'] = !isset($response['g7']['m12']) ? $this->newPedigree('g7.m12','doe') : $this->newPedigree('g7.m12','doe', $response['g7']['m12']);
			
			$response['g7']['f13'] = !isset($response['g7']['f13']) ? $this->newPedigree('g7.f13','buck') : $this->newPedigree('g7.f13','buck', $response['g7']['f13']);
            $response['g7']['m13'] = !isset($response['g7']['m13']) ? $this->newPedigree('g7.m13','doe') : $this->newPedigree('g7.m13','doe', $response['g7']['m13']);

            $response['g7']['f14'] = !isset($response['g7']['f14']) ? $this->newPedigree('g7.f14','buck') : $this->newPedigree('g7.f14','buck',$response['g7']['f14']);
            $response['g7']['m14'] = !isset($response['g7']['m14']) ? $this->newPedigree('g7.m14','doe') : $this->newPedigree('g7.m14','doe', $response['g7']['m14']);

            $response['g7']['f15'] = !isset($response['g7']['f15']) ? $this->newPedigree('g7.f15','buck') : $this->newPedigree('g7.f15','buck', $response['g7']['f15']);
            $response['g7']['m15'] = !isset($response['g7']['m15']) ? $this->newPedigree('g7.m15','doe') : $this->newPedigree('g7.m15','doe', $response['g7']['m15']);

            $response['g7']['f16'] = !isset($response['g7']['f16']) ? $this->newPedigree('g7.f16','buck') : $this->newPedigree('g7.f16','buck', $response['g7']['f16']);
            $response['g7']['m16'] = !isset($response['g7']['m16']) ? $this->newPedigree('g7.m16','doe') : $this->newPedigree('g7.m16','doe', $response['g7']['m16']);
			
			$response['g7']['f17'] = !isset($response['g7']['f17']) ? $this->newPedigree('g7.f17','buck') : $this->newPedigree('g7.f17','buck', $response['g7']['f17']);
            $response['g7']['m17'] = !isset($response['g7']['m17']) ? $this->newPedigree('g7.m17','doe') : $this->newPedigree('g7.m17','doe', $response['g7']['m17']);

            $response['g7']['f18'] = !isset($response['g7']['f18']) ? $this->newPedigree('g7.f18','buck') : $this->newPedigree('g7.f18','buck',$response['g7']['f18']);
            $response['g7']['m18'] = !isset($response['g7']['m18']) ? $this->newPedigree('g7.m18','doe') : $this->newPedigree('g7.m18','doe', $response['g7']['m18']);

            $response['g7']['f19'] = !isset($response['g7']['f19']) ? $this->newPedigree('g7.f19','buck') : $this->newPedigree('g7.f19','buck', $response['g7']['f19']);
            $response['g7']['m19'] = !isset($response['g7']['m19']) ? $this->newPedigree('g7.m19','doe') : $this->newPedigree('g7.m19','doe', $response['g7']['m19']);

            $response['g7']['f20'] = !isset($response['g7']['f20']) ? $this->newPedigree('g7.f20','buck') : $this->newPedigree('g7.f20','buck', $response['g7']['f20']);
            $response['g7']['m20'] = !isset($response['g7']['m20']) ? $this->newPedigree('g7.m20','doe') : $this->newPedigree('g7.m20','doe', $response['g7']['m20']);
			
			$response['g7']['f21'] = !isset($response['g7']['f21']) ? $this->newPedigree('g7.f21','buck') : $this->newPedigree('g7.f21','buck', $response['g7']['f21']);
            $response['g7']['m21'] = !isset($response['g7']['m21']) ? $this->newPedigree('g7.m21','doe') : $this->newPedigree('g7.m21','doe', $response['g7']['m21']);

            $response['g7']['f22'] = !isset($response['g7']['f22']) ? $this->newPedigree('g7.f22','buck') : $this->newPedigree('g7.f22','buck',$response['g7']['f22']);
            $response['g7']['m22'] = !isset($response['g7']['m22']) ? $this->newPedigree('g7.m22','doe') : $this->newPedigree('g7.m22','doe', $response['g7']['m22']);

            $response['g7']['f23'] = !isset($response['g7']['f23']) ? $this->newPedigree('g7.f23','buck') : $this->newPedigree('g7.f23','buck', $response['g7']['f23']);
            $response['g7']['m23'] = !isset($response['g7']['m23']) ? $this->newPedigree('g7.m23','doe') : $this->newPedigree('g7.m23','doe', $response['g7']['m23']);

            $response['g7']['f24'] = !isset($response['g7']['f24']) ? $this->newPedigree('g7.f24','buck') : $this->newPedigree('g7.f24','buck', $response['g7']['f24']);
            $response['g7']['m24'] = !isset($response['g7']['m24']) ? $this->newPedigree('g7.m24','doe') : $this->newPedigree('g7.m24','doe', $response['g7']['m24']);
			
			$response['g7']['f25'] = !isset($response['g7']['f25']) ? $this->newPedigree('g7.f25','buck') : $this->newPedigree('g7.f25','buck', $response['g7']['f25']);
            $response['g7']['m25'] = !isset($response['g7']['m25']) ? $this->newPedigree('g7.m25','doe') : $this->newPedigree('g7.m25','doe', $response['g7']['m25']);

            $response['g7']['f26'] = !isset($response['g7']['f26']) ? $this->newPedigree('g7.f26','buck') : $this->newPedigree('g7.f26','buck',$response['g7']['f26']);
            $response['g7']['m26'] = !isset($response['g7']['m26']) ? $this->newPedigree('g7.m26','doe') : $this->newPedigree('g7.m26','doe', $response['g7']['m26']);

            $response['g7']['f27'] = !isset($response['g7']['f27']) ? $this->newPedigree('g7.f27','buck') : $this->newPedigree('g7.f27','buck', $response['g7']['f27']);
            $response['g7']['m27'] = !isset($response['g7']['m27']) ? $this->newPedigree('g7.m27','doe') : $this->newPedigree('g7.m27','doe', $response['g7']['m27']);

            $response['g7']['f28'] = !isset($response['g7']['f28']) ? $this->newPedigree('g7.f28','buck') : $this->newPedigree('g7.f28','buck', $response['g7']['f28']);
            $response['g7']['m28'] = !isset($response['g7']['m28']) ? $this->newPedigree('g7.m28','doe') : $this->newPedigree('g7.m28','doe', $response['g7']['m28']);
			
			$response['g7']['f29'] = !isset($response['g7']['f29']) ? $this->newPedigree('g7.f29','buck') : $this->newPedigree('g7.f29','buck', $response['g7']['f29']);
            $response['g7']['m29'] = !isset($response['g7']['m29']) ? $this->newPedigree('g7.m29','doe') : $this->newPedigree('g7.m29','doe', $response['g7']['m29']);
			
			$response['g7']['f30'] = !isset($response['g7']['f30']) ? $this->newPedigree('g7.f30','buck') : $this->newPedigree('g7.f30','buck', $response['g7']['f30']);
            $response['g7']['m30'] = !isset($response['g7']['m30']) ? $this->newPedigree('g7.m30','doe') : $this->newPedigree('g7.m30','doe', $response['g7']['m30']);

            $response['g7']['f31'] = !isset($response['g7']['f31']) ? $this->newPedigree('g7.f31','buck') : $this->newPedigree('g7.f31','buck',$response['g7']['f31']);
            $response['g7']['m31'] = !isset($response['g7']['m31']) ? $this->newPedigree('g7.m31','doe') : $this->newPedigree('g7.m31','doe', $response['g7']['m31']);

            $response['g7']['f32'] = !isset($response['g7']['f32']) ? $this->newPedigree('g7.f32','buck') : $this->newPedigree('g7.f32','buck', $response['g7']['f32']);
            $response['g7']['m32'] = !isset($response['g7']['m32']) ? $this->newPedigree('g7.m32','doe') : $this->newPedigree('g7.m32','doe', $response['g7']['m32']);
        }*/
		
        /*switch($g){
            case 2:
                unset($response['g3']);
                unset($response['g4']);
				unset($response['g5']);
				unset($response['g6']);
				unset($response['g7']);
                break;
            case 3:
                unset($response['g4']);
				unset($response['g5']);
				unset($response['g6']);
				unset($response['g7']);
                break;
			case 4:
				unset($response['g5']);
				unset($response['g6']);
				unset($response['g7']);
                break;	
			case 5:
				unset($response['g6']);
				unset($response['g7']);
                break;
			case 6:
				unset($response['g7']);
                break;		
        }*/


        //return $response;
    }

    public function getTokenAttribute()
    {
        return BaseIntEncoder::encode($this->id);
    }


    public function pedigrees()
    {
        return $this->hasMany(Pedigree::class);
    }

    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, 'associated');
    }

    public function totalWeighs(){
        return $this->litters()->sum('total_weight')?:0;
    }

    public function getMissesCountAttribute()
    {
        return $this->plans()->where('missed', 1)->count();
    }

    public function updatePedi($rabbit, $pedi)
    {
        dd($rabbit, $pedi);
    }
}
