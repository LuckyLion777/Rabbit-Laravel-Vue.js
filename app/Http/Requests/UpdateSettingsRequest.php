<?php

namespace App\Http\Requests;

use Auth;

class UpdateSettingsRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->get('new_password')) {
            $password = $this->get('password');
            $user     = $this->route('users');
            $email    = $user->email;
            if (($user = Auth::user()) && !$user->password || Auth::attempt(compact('email', 'password'))) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    public function forbiddenResponse()
    {
        return response()->json(['password' => ['Old password is not correct']], 403);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules['email'] = 'required|email|unique:users,email,' . auth()->user()->id;
        $rules['currency'] = 'required';
        if ($this->get('new_password')) {
            $rules['new_password'] = 'min:6|required_with:password';
            if ($this->get('new_password') != $this->get('new_password_confirmation')) {
                $rules['password_confirmation_pass'] = 'required';
            }
        }

        return $rules;
    }

    public function messages()
    {
        $messages['password_confirmation_pass.required'] = 'Password and confirmation are not equal';

        return $messages;
    }
}
