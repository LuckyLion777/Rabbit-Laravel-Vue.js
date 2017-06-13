<?php

namespace App\Console\Commands;

use App\Events\EventHasGone;
use App\Models\Event;
use Illuminate\Console\Command;

class RemoveArchivedNotClosedEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove-archived-not-closed-events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = Event::query()->with('users')->where('archived', '1')->where('closed', 0)->get();
        $total = $events->count(); $done = 0; $reported = 0;

        $this->output->writeln("Events to process: {$events->count()}");

        foreach ($events as $event) {
            if ($event->isRecurring()) {
                $event->recurringEvents()->delete();
            }
            if ($user = $event->users->first()) {
                \Auth::setUser($user);
                event(new EventHasGone($event, $user));
            }
            $event->delete();

            $percentage = 100 * (++ $done) / $total;
            if ($percentage >= 10 * ($reported + 1)) {
                $reported += 1;
                $percentage = round($percentage);
                $this->output->writeln("\t{$percentage}% done...");
            }
        };

        $this->output->writeln("Done!");
    }
}
