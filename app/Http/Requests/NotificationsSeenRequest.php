<?php

namespace App\Http\Requests;

class NotificationsSeenRequest extends Request
{
    public function authorize()
    {
        return \Auth::check();
    }

    public function rules()
    {
        return [];
    }
}
