<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Push\Contracts\Pusher;
use App\Push\Message;
use Illuminate\Console\Command;

class NotifyAboutUpcoming extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:upcoming';

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
     * @param Pusher $pusher
     */
    public function handle(Pusher $pusher)
    {
        $users = User::all()->load('actualTodayEvents');

        foreach($users as $user){
            /* @var $user User */
            if (!$user->isPremium()) continue;

            $this->info($user->name);

            if ($count = $user->actualTodayEvents()->count()) {
                $this->info('Found ' . $count . ' events, sending message...');

                $msg = \Lang::choice('notifications.schedule.have_tasks', $count, compact('count'));
                $message = new Message('Hutch Tasks', "{$msg}.\nClick to view your Hutch Schedule.");

                $res = $pusher->sendToUser($user, $message);
                if ($res) {
                    $this->info('Successfully sent ' . $res . ' notifications');
                } else {
                    $this->info('Could not send any messages');
                }
            } else {
                $this->info('Nothing to send, skipping');
            }
        }
    }
}
