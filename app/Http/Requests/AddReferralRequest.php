<?php

namespace App\Http\Requests;

class AddReferralRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'bail|required|email|referred'
        ];
    }
}
