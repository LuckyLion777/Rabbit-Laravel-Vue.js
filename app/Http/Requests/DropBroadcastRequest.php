<?php

namespace App\Http\Requests;

use App\Models\BroadcastMessage;

class DropBroadcastRequest extends Request
{
    public function authorize()
    {
        return !!BroadcastMessage::active();
    }

    public function rules()
    {
        return [];
    }
}
