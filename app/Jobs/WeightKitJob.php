<?php

namespace App\Jobs;

use App\Events\KitWasWeighed;
use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WeightKitJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;
    /**
     * @var
     */
    public $kit;
    /**
     * @var
     */
    public $current_weight;
    public $current_weight_date;
    public $first;

    /**
     * Create a new job instance.
     *
     * @param $kit
     * @param $current_weight
     * @param $current_weight_date
     */
    public function __construct($kit, $current_weight, $current_weight_date='', $first = false)
    {
        $this->kit            = $kit;
        $this->current_weight = $current_weight;
        $this->current_weight_date = $current_weight_date;
        $this->first = $first;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $weight = $this->kit->weight;
        if (!$weight) {
            $weight = [$this->current_weight];
        } else {
//            if (count($weight) >= 3)                // old - only 3 weigh
//                $weight[2] = $this->current_weight;
//            else
            array_push($weight, $this->current_weight);
        }
        $this->kit->weight         = $weight;
        $this->kit->current_weight = $this->current_weight;
        
        if ($this->current_weight_date != '') {
            $weight_date = $this->kit->weight_date;
            if (!$weight_date) {
                $weight_date = [];
            }

            // Handle existing weights without date
            $diff_length = (count($weight) - 1) - count($weight_date);
            for ($i=1;$i<=$diff_length;$i++) {
                array_push($weight_date, 'null');
            }
            array_push($weight_date, $this->current_weight_date);

            // if (!$weight_date) {
            //     $weight_date = [$this->current_weight_date];
            // } else {
            //     array_push($weight_date, $this->current_weight_date);
            // }
            $this->kit->weight_date = $weight_date;
        }
        $this->kit->update();
    }
}
