<?php

namespace App\Http\Requests;

class CreateBroadcastRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'content' => 'required',
        ];
    }
}
