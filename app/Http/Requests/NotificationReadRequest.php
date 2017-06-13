<?php

namespace App\Http\Requests;

class NotificationReadRequest extends Request
{
    public function authorize()
    {
        return \Auth::check();
    }

    public function rules()
    {
        return [
            'read' => 'required|boolean'
        ];
    }
}
