<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Stripe\Stripe;

class UpdateCustomers extends Command
{
    const EPSILON = 0.0001;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:update';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $users = User::query()->whereNotNull('stripe_id')->get();
        /* @var $users User[]|Collection */
        $milestone = 0.1; $done = 0; $total = $users->count();

        foreach ($users as $user) {
            $user->updateStripeCustomer();

            $progress = (++$done) / $total;
            if ($progress >= $milestone - static::EPSILON) {
                $this->output->writeln(sprintf("%d%% ready...", 100 * $progress));
                $milestone += 0.1;
            }
        }
    }
}
