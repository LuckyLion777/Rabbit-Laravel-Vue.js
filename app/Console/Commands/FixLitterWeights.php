<?php

namespace App\Console\Commands;

use App\Models\Litter;
use Illuminate\Console\Command;

class FixLitterWeights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'litter:weights';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('Command for updating litter weights. Do you wish to continue?')) {
            $litters = Litter::with('survivedKits')->get();
            foreach ($litters as $litter) {
                if (!$litter->total_weight || !$litter->average_weight || $litter->total_weight == '0.00' || $litter->average_weight == '0.00') {
                    if($litter->survivedKits()->count()){
                        foreach($litter->survivedKits as $kit){
                            if($kit->weight && $kit->current_weight == '0.00' || !$kit->current_weight){
                                if($kit->weight[0] == null){
                                    $kit->weight = null;
                                } else
                                if (is_array($kit->weight)){
                                    $weights = $kit->weight;
                                    $kit->current_weight = end($weights);
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
    }
}
