<?php

namespace App\Http\Requests;

class TransferRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|not_user_email',
        ];
    }
}
