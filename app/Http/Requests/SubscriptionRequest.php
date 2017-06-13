<?php

namespace App\Http\Requests;

class SubscriptionRequest extends Request
{
    public function authorize()
    {
        $subscription = $this->user()->subscription();
        return !$subscription || !$subscription->valid() || $subscription->stripe_plan != 'forever';
    }

    public function rules()
    {
        return [
            'token' => 'required_unless_subscribed',
            'plan_id' => 'required|valid_plan'
        ];
    }
}
