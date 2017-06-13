<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\User;
use Mail;

class WeeklyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:digest';

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
        $today = Carbon::now()->startOfDay();
        $users = User::where('digest_day', $today->dayOfWeek)->where(function($q) use($today) {
            $q->where('last_digest_at', '<=', $today->subDays(6)->toDateString());
            $q->orWhere('last_digest_at', null);
        })->get();

        foreach($users as $user){
            /* @var $user User */
            if (!$user->isPremium()) continue;
            
            $this->info($user->name);
            Mail::queue('emails.digest', ['user' => $user], function ($m) use ($user) {
                $m->from(config('mail.from.address'), config('mail.from.name'));
                $m->to($user->email, $user->name)->subject('Hutch Weekly Digest');
            });
            $user->last_digest_at = Carbon::now();
            $user->save();
        }

    }
}
