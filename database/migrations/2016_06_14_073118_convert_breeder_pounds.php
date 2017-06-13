<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\User;

class ConvertBreederPounds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = User::where('general_weight_units', 'Pound/Ounces')->get();

        foreach($users as $user){
            foreach($user->breeders as $breeder){
                if($breeder->weight){
                    $weight = $breeder->weight;
                    $breeder->weight = $this->convertToOz($weight);
                    $breeder->save();
                }
                foreach($breeder->pedigrees as $pedigree){
                    $weight = $pedigree->weight;
                    $pedigree->weight = $this->convertToOz($weight);
                    $pedigree->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $users = User::where('general_weight_units', 'Pound/Ounces')->get();

        foreach($users as $user){
            foreach($user->breeders as $breeder){

                if($breeder->weight){
                    $weight = $breeder->weight;
                    $breeder->weight = $this->convertToOldPounds($weight);
                    $breeder->save();
                }
                foreach($breeder->pedigrees as $pedigree){
                    $weight = $pedigree->weight;
                    $pedigree->weight = $this->convertToOldPounds($weight);
                    $pedigree->save();
                }
            }
        }
    }

    protected function convertToOldPounds($weight){
        $tmp = explode(".", (float)$weight/16);
        $newWeight = (string)$tmp[0];
        if(isset($tmp[1])){
            $newWeight .= '.' . ($weight - $tmp[0]*16);
        }
        return (float)$newWeight;
    }

    protected function convertToOz($weight){
        $tmp = explode(".", $weight);
        $newWeight = ($tmp[0] * 16);
        $newWeight += isset($tmp[1]) ? $tmp[1] : 0;
        return (float)$newWeight;
    }


}
