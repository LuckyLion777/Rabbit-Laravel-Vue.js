<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends CashierController
{
    /**
     * Handle a new subscription from dashboard.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if ($user) {
            $data = $payload['data']['object'];
            $subscription = $user->subscriptions->first(function ($key, $subscription) use ($data) {
                return $subscription->created_at->getTimestamp() > $data['created']
                        || $subscription->stripe_id === $data['id'];
            });
            if (!$subscription) {
                $user->subscriptions()->create([
                    'name' => 'default',
                    'stripe_id' => $data['id'],
                    'stripe_plan' => $data['plan']['id'],
                    'quantity' => $data['quantity'],
                    'trial_ends_at' => empty($data['trial_end'])
                                            ? null
                                            : Carbon::createFromTimestamp($data['trial_end']),
                    'ends_at' => null,
                ]);
            }
        }

        return new Response('Webhook Handled', 200);
    }
}
