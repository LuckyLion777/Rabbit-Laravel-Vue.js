<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionPreviewRequest;
use App\Http\Requests\SubscriptionRequest;
use App\Models\Subscription\Plan;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Laravel\Cashier\Invoice;
use Stripe\Coupon;
use Stripe\Error\Card;
use Stripe\Error\InvalidRequest;
use Stripe\Stripe;

class SubscriptionController extends Controller
{
    public function show(Guard $auth)
    {
        $user = $auth->user();
        /* @var $user User */

        return response()->json([
            'unsubscribed' => !$user->isSubscribed(),
            'coupon' => '',
            'plan' => $this->getSubscriptionPlan($user),
            'card' => $this->getCardData($user),
        ]);
    }

    private function previewUpgrade(SubscriptionPreviewRequest $request)
    {
        try {
            $preview = \Stripe\Invoice::upcoming([
                'customer' => $request->user()->stripe_id,
                'coupon' => $request->get('code') ?: null,
                'subscription' => $request->user()->subscription()->stripe_id,
                'subscription_plan' => $request->get('plan_id'),
                'subscription_prorate' => true,
            ]);
            $lines = [];
            if ($preview->starting_balance) {
                $lines[] = ['title' => 'Starting balance', 'amount' => $preview->starting_balance];
            }
            $plan = null;
            foreach ($preview->lines->data as $line) {
                if ($line->type == 'subscription') {
                    $plan = $line->plan;
                }
                if (!$line->proration) {
                    continue;
                }
                $lines[] = [
                    'title' => preg_split('/after \d{1,2} [a-zA-Z]{3,6} \d{4}$/', $line->description)[0],
                    'amount' => $line->amount,
                ];
            }

            $setup = intval($plan->metadata->setup_price);
            if (($coupon = $preview->discount ? $preview->discount->coupon : null) && $setup) {
                $setup -= $setup * $coupon->precent_off;
            }
            if ($setup) {
                $lines[] = [
                    'title' => $plan->name . ' (One-time payment)',
                    'amount' => $setup
                ];
            }

            return response()->json([
                'plan' => $plan->id,
                'coupon' => $coupon ? $coupon->id : null,
                'lines' => $lines
            ]);
        } catch (InvalidRequest $e) {
            return response()->json([
                'plan_id' => [$e->getMessage()],
            ]);
        }
    }

    public function preview(SubscriptionPreviewRequest $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        if ($request->user()->subscribed()) {
            return $this->previewUpgrade($request);
        }

        try {
            $plan = \Stripe\Plan::retrieve($request->get('plan_id'));
        } catch (InvalidRequest $e) {
            return response()->json(['plan_id' => ['Invalid plan']], 422);
        }

        $coupon = null;
        if ($request->has('code')) {
            try {
                $coupon = Coupon::retrieve(trim($request->get('code')));
                if (!$coupon->valid) {
                    throw new InvalidRequest('This code is no longer valid', 'coupon');
                }
            } catch (InvalidRequest $e) {
                return response()->json(['code' => ['Invalid code']], 422);
            }
        }

        $setup_price = intval(@$plan->metadata->setup_price) / 100;
        $amount = $plan->amount / 100;
        $discount = 0;
        if ($coupon) {
            $discount = $coupon->amount_off ?: round(($amount + $setup_price) * $coupon->percent_off / 100, 2);
        }

        return response()->json([
                'lines' => null, // Only for upgrade (look previewUpgrade)
                'plan' => $plan->name,
                'coupon' => $coupon ? $coupon->id : null,
            ] + compact('amount', 'discount', 'setup_price'));
    }

    public function store(SubscriptionRequest $request)
    {
        $user = $request->user();
        try {
            $setup_price = Plan::find($request->get('plan_id'))->getSetupPrice();
            Stripe::setApiKey(config('services.stripe.secret'));
            if ($user->hasStripeId()) {
                $customer = $user->asStripeCustomer();

                $coupon = @$customer->discount->coupon;
                if ($request->has('code')) {
                    $coupon = Coupon::retrieve($request->get('code'));
                }
                $discount = (int) (@$coupon->amount_off ?: @$coupon->percent_off * $setup_price * 0.01);
                $setup_price_discounted = max($setup_price - $discount, 0);

                $customer->account_balance += $setup_price_discounted;
                if ($request->has('code')) {
                    $customer->coupon = $request->get('code');
                }
                $customer->save();
            } else {
                $coupon = $request->has('code') ? Coupon::retrieve($request->get('code')) : null;
                $discount = (int) (@$coupon->amount_off ?: @$coupon->percent_off * $setup_price * 0.01);
                $setup_price_discounted = max($setup_price - $discount, 0);

                $customer = $user->createAsStripeCustomer(null, array_filter([
                    'account_balance' => $setup_price_discounted,
                    'coupon' => $request->get('code'),
                ]));
            }

            $subscription = $user->subscription();
            if (!$subscription || !$subscription->valid()) {
                $user->newSubscription('default', $request->get('plan_id'))
                    ->create($request->get('token'));
            } else {
                if ($request->has('token')) {
                    $user->updateCard($request->get('token'));
                }
                if ($subscription->onGracePeriod()) {
                    $subscription->resume();
                }
                if ($subscription->stripe_plan !== $request->get('plan_id')) {
                    try {
                        $subscription->swap($request->get('plan_id'));
                    } catch (\Exception $e) {
                        // Firstly, return paid setup price and swap back the plan
                        $customer->refresh();
                        if ($setup_price_discounted != 0) {
                            $customer->account_balance -= $setup_price_discounted;
                            $customer->save();
                        }
                        $subscription->swap($subscription->stripe_plan);

                        // The problem now is user will have a failed invoice while
                        // having some credit amount - we do not this.
                        //
                        // The solution is to forgive the invoice then create new one just the same
                        // which will be paid out from the credit.

                        try { $user->retryInvoices(); }
                        catch (\Exception $e) {
                            \Log::error(print_r([
                                'what' => 'Cannot fix failed invoice!',
                                'class' => get_class($e),
                                'code' => $e->getCode(),
                                'message' => $e->getMessage(),
                            ], true));
                        }

                        throw $e;
                    }
                }
            }
        } catch (InvalidRequest $e) {
            $param = $e->getStripeParam() === 'coupon' ? 'code' : 'number';
            return response()->json([$param => [$e->getMessage()]], 422);
        } catch (Card $e) {
            return response()->json(['number' => [$e->getMessage()]], 422);
        }

        $this->sendInvoices($request->user());

        $user->load('subscriptions');
        return response()->json([
            'plan' => $this->getSubscriptionPlan($user),
            'card' => $this->getCardData($user),
        ]);
    }

    public function destroy(Guard $auth)
    {
        $user = $auth->user();
        /* @var $user User */
        if (($subscription = $user->subscription()) && $subscription->active()) {
            $subscription->cancel();
        }

        $this->sendInvoices($auth->user());

        $user->load('subscriptions');

        return response()->json([
            'plan' => $this->getSubscriptionPlan($user),
            'card' => $this->getCardData($user),
        ]);
    }

    /**
     * @param User $user
     * @return array|null
     */
    private function getSubscriptionPlan($user)
    {
        $response = [
            'on_trial' => $user->isTrial(),
            'trial_ends_at' => $user->trial_ends_at ? $user->trial_ends_at->getTimestamp() : null,
        ];

        if (!($subscription = $user->subscription()) || !$subscription->valid()) {
            return $response;
        }
        return [
            'id' => $subscription->stripe_plan,
            'level' => Plan::find($subscription->stripe_plan)->getLevel(),
            'on_grace' => $subscription->onGracePeriod(),
            'ends_at' => $subscription->ends_at ? $subscription->ends_at->getTimestamp() : null,
        ] + $response;
    }

    /**
     * @param User $user
     * @return array|null
     */
    private function getCardData($user)
    {
        return  $user->card_brand ? [
            'brand' => $user->card_brand,
            'last_four' => $user->card_last_four,
            'attached' => true,
        ] : null;
    }

    private function sendInvoices(User $user)
    {
        $new = !$user->last_sent_invoice;
        foreach ($user->invoices() as $invoice) {
            /* @var $invoice Invoice */
            if (!$new) {
                if ($invoice->asStripeInvoice()->id === $user->last_sent_invoice) {
                    $new = true;
                }
                continue;
            }

            $user->sendInvoice($invoice);
        }

        if (isset($invoice)) {
            $user->last_sent_invoice = $invoice->asStripeInvoice()->id;
            $user->save();
        }
    }
}
