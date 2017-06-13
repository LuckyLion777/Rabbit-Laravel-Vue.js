<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class SetReferrerConfirmedRequest extends Request
{
    public function authorize()
    {
        // wrong user will be detected in validation (to show good message),
        // anonymous with correct request will be remembered to have made it
        // and request will be processed when logging in
        return true;
    }

    public function rules()
    {
        return [
            'confirm' => 'required|referrer_confirm',
            'email' => 'bail|required|email|referrer:confirm',
            'me' => 'bail|required|email|referred:confirm',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->session()->set('callout_error', collect($validator->getMessageBag())->first()[0]);
        parent::failedValidation($validator);
    }
}
