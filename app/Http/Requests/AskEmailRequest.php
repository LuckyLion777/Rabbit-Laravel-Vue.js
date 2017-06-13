<?php

namespace App\Http\Requests;

class AskEmailRequest extends Request
{
    public function authorize()
    {
        return !$this->user();
    }

    public function rules()
    {
        return [
            'social' => 'required|exists:social_accounts,id'
        ];
    }
}
