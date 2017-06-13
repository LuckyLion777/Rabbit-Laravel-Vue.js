<?php

namespace App\Console\Commands;

use App\Push\Contracts\Pusher;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Mail\Message;
use Mail;

class TrialExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:trial-expiration';

    /**
     * Execute the console command.
     *
     * @param Pusher $pusher
     * @return mixed
     */
    public function handle(Pusher $pusher)
    {
        $users = User::whereBetween('trial_ends_at', [Carbon::now(), Carbon::now()->addDay()])
                    ->whereNull('trial_notified_at')->get();
        foreach ($users as $user) {
            /* @var $user User */
            $user->trial_notified_at = Carbon::now();
            if ($user->subscribed()) {
                $user->save();
                continue;
            }

            Mail::queue('emails.trial-expires', ['user' => $user], function (Message $message) use ($user) {
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->to($user->email, $user->name)->subject('Your Hutch Trial Expires Tomorrow');
            });
            $user->save();

            $message = new \App\Push\Message(
                'Your Hutch is Expiring!',
                'Your trial expires tomorrow! Click to subscribe now.'
            );
            $message->url = 'https://htch.us/#!/account';
            $pusher->sendToUser($user, $message);
        }
    }
}
