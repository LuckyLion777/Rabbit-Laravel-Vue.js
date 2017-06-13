<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use App\Models\Device;

class HasDeviceIdRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'device_id' => 'required' // It is not a violation if device_id does not exist
        ];
    }

    /**
     * @return Device|null
     */
    public function getDevice()
    {
        return Device::whereDeviceId($this['device_id'])->first();
    }
}
