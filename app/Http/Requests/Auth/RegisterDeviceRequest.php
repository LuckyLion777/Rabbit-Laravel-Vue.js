<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class RegisterDeviceRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'    => 'required|email',
            'password' => 'required|min:6',
            'device_id' => 'required' // No longer required to be unique because client can login as another user
        ];
    }
}
