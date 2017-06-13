<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CreditReferrers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrers:credit';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $referreds = User::whereNotNull('referred_by_id')->whereNull('referrer_credited_at')
            // this will filter out users not having subscribed
            ->whereNotNull('card_last_four');
        /* @var $referreds User[]|Builder */

        $this->info('Found ' . $referreds->count() . ' referred users not yet having brought credit to referrer.');

        $referreds->each(function (User $referred) {
            if (!$referred->subscribed()) {
                return;
            }
            $referrer = $referred->referredBy;
            /* @var $referrer User */
            if (!$referrer->subscribed()) {
                return;
            }
            $this->info('User ' . $referred->name . ' <' . $referred->email . '> referred by ' .
                            $referrer->name . ' <' . $referrer->email . '>');
            try {
                $customer = $referrer->asStripeCustomer();
                if ($amount = max(0, min($referrer->getReferralsCap($customer) - $referrer->referrals_credited, 500))) {
                    if ($referrer->getPlan()->getAmount()) {
                        $this->info('Crediting $' . ($amount / 100));
                        $real = $referrer->credit($amount, $customer);
                    } else {
                        $this->info('Refunding $' . ($amount / 100));
                        $real = $referrer->refund($amount, $customer);
                    }

                    if ($real) {
                        $this->info('Was able to refund/credit $' . ($real / 100));
                        $referred->referrer_credited_at = Carbon::now();
                        $referred->save();

                        $referrer->referrals_credited += $real;
                        $referrer->save();
                    } else {
                        $this->warn('Could not refund/credit any amount');
                    }
                }
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }

        }, 100);
    }
}
