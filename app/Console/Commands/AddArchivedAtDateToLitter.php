<?php

namespace App\Console\Commands;

use App\Models\Litter;
use Illuminate\Console\Command;

class AddArchivedAtDateToLitter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'litter:archived_at';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup litter archived_at date from update_at field';

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
        if ($this->confirm('Command for setup litter archived date. Do you wish to continue?')) {
            $litters = Litter::all();
            foreach ($litters as $litter) {
                if ($litter->archived && !$litter->archived_at) {
                    $litter->archived_at = $litter->updated_at;
                    $litter->update();
                }
            }
        }
    }
}
