<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Cashier\Subscription;
use Stripe\Plan;
use Stripe\Stripe;

class RefreshSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:refresh';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        foreach (Plan::all()->autoPagingIterator() as $plan) {
            $subscriptions = Subscription::where('stripe_plan', $plan->id)->whereNull('ends_at')
                ->where(function (Builder $query) use ($plan) {
                    $query->whereNull('subscribed_at')
                        ->orWhere('subscribed_at', '<=', Carbon::createFromTimestamp($plan->created));
                });
            foreach ($subscriptions->get() as $subscription) {
                /* @var $subscription Subscription */
                $stripeSubscription = $subscription->asStripeSubscription();
                $stripeSubscription->plan = $plan->id;
                $stripeSubscription->prorate = false;
                $stripeSubscription->save();

                $subscription->subscribed_at = Carbon::now();
                $subscription->save();
            }
        }
    }
}
