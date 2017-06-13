<?php

namespace App\Http\Requests;

class SubscriptionPreviewRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'plan_id' => 'required|valid_plan'
        ];
    }
}
