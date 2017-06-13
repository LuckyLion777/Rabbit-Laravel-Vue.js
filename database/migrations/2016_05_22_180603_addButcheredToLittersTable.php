<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddButcheredToLittersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('litters', function (Blueprint $table) {
            $table->boolean('butchered')->nullable()->default(0)->after('archived_at');
        });

        $litters = \App\Models\Litter::with('rabbitKits')->get();
        foreach($litters as $litter){
            if($litter->butchered_at){
                $litter->butchered = 1;
                $litter->save();
            } else {
                if(!$litter->rabbitKits()->count()){
                    $litter->butchered = 1;
                    $born = \Carbon\Carbon::createFromFormat('m/d/Y', $litter->born);
                    $litter->butchered_at = $born->addDays(90);
                    $litter->save();
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
        Schema::table('litters', function (Blueprint $table) {
            $table->dropColumn('butchered');
        });
    }
}
