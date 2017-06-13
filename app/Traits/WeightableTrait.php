<?php

namespace App\Traits;
use App\Models\RabbitBreeder;

/**
 * For models having weigh slug.
 * Expects presence of a method `getWeightUnits` (which should be unit, or space for lbs/oz (special case)
 */
trait WeightableTrait
{
    /*public function getWeightSlugAttribute($weight = null)
    {
        if ($weight === null) {
            $weight = $this->weight;
        }
        $units = $this->getWeightUnits();
		$generalWeightUnitLabel = $this->getWeightUnitAttribute();
        if ($units === null) return $weight;
        if ($units === '') return $this->calcLbsOunces($weight,$generalWeightUnitLabel);
        return $weight . ' ' . $units;
    }*/

	public function getWeightSlugAttribute($weight = null)
    {
        if ($weight === null) {
            $weight = isset($this->weight) ? $this->weight : 0;
        }
        $units = $this->getWeightUnits();
        $generalWeightUnitLabel = $this->getWeightUnitAttribute();
        if ($units === null) return $weight;

        if($generalWeightUnitLabel == 'Ounces'){
            return $this->calOunces($weight);
        }
        elseif($generalWeightUnitLabel == 'Grams'){
            return $this->calGrams($weight);
        }
        elseif($generalWeightUnitLabel == 'Pound/Ounces'){
            return $this->calPoundOunces($weight);
        }
        elseif($generalWeightUnitLabel == 'Pounds'){
            return $this->calPounds($weight);
        }
        elseif($generalWeightUnitLabel == 'Kilograms'){
            return $this->calKilograms($weight);
        }
        
    }
    
    private function calOunces($weight)
    {
        $cal = floatval($weight);
        //$tmp = explode(".", $cal);
        $txt = $cal > 0 ? round($cal,2) . " oz " : '';
        return $txt;
    }
    
    private function calGrams($weight)
    {
        //$cal = floatval($weight) / 0.035274;
        $cal = floatval($weight);
        //$tmp = explode(".", $cal);
        $txt = $cal > 0 ? round($cal,2) . " g " : '';
        return $txt;
    }
    
    private function calPoundOunces($weight)
    {
        $tLbs = floatval($weight) / 16;
        $tmp = explode(".", $tLbs);
        $lbs = (int)$tmp[0];
        $oz = ((float)(($tLbs - $lbs) * 16));
        $txt = '';
        $txt .= $lbs > 0 ? $lbs . " lbs " : '';
        $txt .= $oz > 0 ? $oz . " oz " : '';
        
        return $txt;
    }
    
    private function calPounds($weight)
    {
        //$cal = floatval($weight) * 0.062500;
        $cal = floatval($weight);
        //$tmp = explode(".", $cal);
        $txt = $cal > 0 ? round($cal,2) . " lbs " : '';
        return $txt;
    }
    
    private function calKilograms($weight)
    {
        //$cal = floatval($weight) / 35.274;
        $cal = floatval($weight);
        //$tmp = explode(".", $cal);
        $txt = $cal > 0 ? round($cal,2) . " kg " : '';
        return $txt;
    }

    public function getWeightConvertedArrayAttribute($weight = null)   //only for pounds/ounces
    {
        if ($weight === null) {
            $weight = $this->weight;
        }
        $units = $this->getWeightUnits();
        if ($units === '') return $this->calcLbsOuncesToArray($weight);
    }

    private function calcLbsOuncesToArray($weight)
    {
        $tLbs = floatval($weight) / 16;
        $tmp = explode(".", $tLbs);
        $lbs = (int)$tmp[0];
        $oz = ((float)(($tLbs - $lbs) * 16));
        return [$lbs, $oz];
    }

    public function getWeightAttribute($weight = null)
    {
        if ($weight != null) {
            //$weight = isset($this->weight) ? $this->weight : 0;
        
        
            $units = $this->getWeightUnits();
            $generalWeightUnitLabel = $this->getWeightUnitAttribute();
            if ($units === null) return $weight;

            if($generalWeightUnitLabel == 'Ounces'){
                return $this->getCalOunces($weight);
            }
            elseif($generalWeightUnitLabel == 'Grams'){
                return $this->getCalGrams($weight);
            }
            elseif($generalWeightUnitLabel == 'Pound/Ounces'){
                return $this->getCalPoundOunces($weight);
            }
            elseif($generalWeightUnitLabel == 'Pounds'){
                return $this->getCalPounds($weight);
            }
            elseif($generalWeightUnitLabel == 'Kilograms'){
                return $this->getCalKilograms($weight);
            }
        }
    
    }

    private function getCalOunces($weight)
    {
        $cal = floatval($weight);
        // $tmp = explode(".", $cal);
        $txt = $cal > 0 ? round($cal,2) : 0;
        return $txt;
    }
    
    private function getCalGrams($weight)
    {
        $cal = floatval($weight) / 0.035274;
        // $tmp = explode(".", $cal);
        $txt = $cal > 0 ? round($cal,2) : 0;
        return $txt;
    }
    
    private function getCalPoundOunces($weight)
    {
        return $weight;
        $tLbs = floatval($weight) / 16;
        $tmp = explode(".", $tLbs);
        $lbs = (int)$tmp[0];
        $oz = ((float)(($tLbs - $lbs) * 16));
        $txt = '';
        $txt .= $lbs > 0 ? $lbs : 0;
        $txt .= $oz > 0 ? $oz : 0;
        
        return $txt;
    }
    
    private function getCalPounds($weight)
    {
        $cal = floatval($weight) * 0.062500;
        // $tmp = explode(".", $cal);
        $txt = $cal > 0 ? round($cal,2) : 0;
        return $txt;
    }
    
    private function getCalKilograms($weight)
    {
        $cal = floatval($weight) / 35.274;
        // $tmp = explode(".", $cal);
        $txt = $cal > 0 ? round($cal,2) : 0;
        return $txt;
    }

    /**
    *   Set weight attribute 
    **/
    public function setWeightAttribute($weight = null)
    {
        if ($weight === null) {
            $weight = isset($this->weight) ? $this->weight : 0;
        }
        $units = $this->getWeightUnits();
        $generalWeightUnitLabel = $this->getWeightUnitAttribute();
        if ($units === null) $this->attributes['weight'] = $weight;

        if($generalWeightUnitLabel == 'Ounces'){
            $this->attributes['weight'] = $weight;
        }
        elseif($generalWeightUnitLabel == 'Grams'){
            $this->calGramsFromOunces($weight);
        }
        elseif($generalWeightUnitLabel == 'Pound/Ounces'){
            $this->calPoundOuncesFromOunces($weight);
        }
        elseif($generalWeightUnitLabel == 'Pounds'){
            $this->calPoundsFromOunces($weight);
        }
        elseif($generalWeightUnitLabel == 'Kilograms'){
            $this->calKilogramsFromOunces($weight);
        }
        
    }
    
    private function calGramsFromOunces($weight)
    {
        $res = floatval($weight) * 0.035274;
        return $this->attributes['weight'] = round($res,2);
    }
    
    private function calPoundOuncesFromOunces($weight)
    {
        return $this->attributes['weight'] = round($weight,2);
    }
    
    private function calPoundsFromOunces($weight)
    {
        $res = floatval($weight) * 16.000;
        return $this->attributes['weight'] = round($res,2);
    }
    
    private function calKilogramsFromOunces($weight)
    {
        $res = floatval($weight) * 35.274;
        return $this->attributes['weight'] = round($res,2);
    }

    /**
    * Set attribute kits related code
    **/
    private function kitCalGramsFromOunces($weight)
    {
        $res = floatval($weight) * 0.035274;
        return round($res,2);
    }
    
    private function kitCalPoundOuncesFromOunces($weight)
    {
        return round($weight,2);
    }
    
    private function kitCalPoundsFromOunces($weight)
    {
        $res = floatval($weight) * 16.000;
        return round($res,2);
    }
    
    private function kitCalKilogramsFromOunces($weight)
    {
        $res = floatval($weight) * 35.274;
        // dd($weight,round($res,2));
        return round($res,2);
    }
    /**
    * Get attribute for kits related code
    **/
    // private function getCalOunces($weight)
    // {
    //     $cal = floatval($weight);
    //     // $tmp = explode(".", $cal);
    //     $txt = $cal > 0 ? round($cal,2) : 0;
    //     return $txt;
    // }
    
    // private function getCalGrams($weight)
    // {
    //     $cal = floatval($weight) / 0.035274;
    //     // $tmp = explode(".", $cal);
    //     $txt = $cal > 0 ? round($cal,2) : 0;
    //     return $txt;
    // }
    
    // private function getCalPoundOunces($weight)
    // {
    //     return $weight;
    //     $tLbs = floatval($weight) / 16;
    //     $tmp = explode(".", $tLbs);
    //     $lbs = (int)$tmp[0];
    //     $oz = ((float)(($tLbs - $lbs) * 16));
    //     $txt = '';
    //     $txt .= $lbs > 0 ? $lbs : 0;
    //     $txt .= $oz > 0 ? $oz : 0;
        
    //     return $txt;
    // }
    
    // private function getCalPounds($weight)
    // {
    //     $cal = floatval($weight) * 0.062500;
    //     // $tmp = explode(".", $cal);
    //     $txt = $cal > 0 ? round($cal,2) : 0;
    //     return $txt;
    // }
    
    // private function getCalKilograms($weight)
    // {
    //     $cal = floatval($weight) / 35.274;
    //     // $tmp = explode(".", $cal);
    //     $txt = $cal > 0 ? round($cal,2) : 0;
    //     return $txt;
    // }
}
