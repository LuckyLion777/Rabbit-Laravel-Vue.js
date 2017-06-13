<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\User;

class ConvertPoundsOuncesToOunces extends Migration
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
            foreach($user->litters as $litter){
                foreach($litter->totalKits as $kit){
                    if($kit->weight){
                        $weights = $kit->weight;
                        $kit->resetWeight();
                        foreach($weights as $weight){
                            $tmp = explode(".", $weight);
                            $newWeight = ($tmp[0] * 16);
                            $newWeight += isset($tmp[1]) ? $tmp[1] : 0;
                            $kit->newWeight($newWeight);
                        }
                        $kit->save();
                    }
                }
                $litter->updateWeights();
                $litter->save();
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
            foreach($user->litters as $litter){
                foreach($litter->totalKits as $kit){
                    if($kit->weight){
                        $weights = $kit->weight;
                        $kit->resetWeight();
                        foreach($weights as $weight){
                            $tmp = explode(".", (float)$weight/16);
                            $newWeight = (string)$tmp[0];
                            if(isset($tmp[1])){
                                $newWeight .= '.' . ($weight - $tmp[0]*16);
                            }

                            $kit->newWeight((float)$newWeight);
                        }
                        $kit->save();
                    }
                }
                $litter->updateWeights();
                $litter->save();
            }
        }
    }
}
