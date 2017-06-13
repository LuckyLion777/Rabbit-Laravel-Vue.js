<?php

namespace App\Http\Requests;

use App\Models\BroadcastMessage;

class UpdateBroadcastRequest extends Request
{
    public function authorize()
    {
        return !!BroadcastMessage::active();
    }

    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'content' => 'required',
        ];
    }
}
