<?php

namespace App\Http\Requests;

class SetEmailRequest extends Request
{
    public function authorize()
    {
        return !$this->user();
    }

    public function rules()
    {
        return [
            'social' => 'required|exists:social_accounts,id',
            'email' => 'required|email|unique:users',
        ];
    }
}
