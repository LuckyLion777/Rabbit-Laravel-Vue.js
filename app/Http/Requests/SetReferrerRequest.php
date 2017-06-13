<?php

namespace App\Http\Requests;

class SetReferrerRequest extends Request
{
    public function authorize()
    {
        return !$this->user()->referredBy;
    }

    public function rules()
    {
        return [
            'email' => 'bail|required|email|referrer'
        ];
    }
}
