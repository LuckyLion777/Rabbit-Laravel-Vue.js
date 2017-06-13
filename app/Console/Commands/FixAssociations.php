<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Litter;
use App\Models\RabbitBreeder;
use App\Models\RecurringEvent;
use App\Repositories\EventRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;

class FixAssociations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-associations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix associations';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tables = [
            'eventables' => 'eventable',
            'litterables' => 'litterable',
            'plannables' => 'plannable',
            'taskables' => 'taskable',
        ];
        $map = [
            'breeder' => RabbitBreeder::class,
            'litter' => Litter::class,
        ];
        foreach ($tables as $table => $field) {
            foreach ($map as $alias => $class) {
                \DB::table($table)->where($field . '_type', '=', $class)
                    ->update([
                        $field . '_type' => $alias,
                    ]);
            }
        }
    }
}
