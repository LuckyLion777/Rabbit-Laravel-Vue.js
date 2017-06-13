<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\DeviceTracker;
use App\Http\Requests\Auth\HasDeviceIdRequest;
use App\Http\Requests\Auth\RegisterDeviceRequest;
use App\Http\Controllers\Controller;
use App\Models\Device;

class AuthDeviceController extends Controller
{
    /**
     * Check if device is registered in the system
     * @param HasDeviceIdRequest $request
     * @return mixed
     */
    public function check(HasDeviceIdRequest $request)
    {
        $device = $request->getDevice();
        return response()->json(['success' => $device && $device->user_id]);
    }

    /**
     * Logs user in if we know this device ID and user associated with it,
     * otherwise stores the deviceId in the session
     *
     * @param HasDeviceIdRequest $request
     * @param DeviceTracker $deviceTracker
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(HasDeviceIdRequest $request, DeviceTracker $deviceTracker)
    {
        $deviceTracker->setDeviceId($request['device_id']);
        if (($device = $request->getDevice()) && $device->user) {
            auth()->login($device->user);
        }
        return redirect()->to('/');
    }

    /**
     * Register new device for user
     * @param RegisterDeviceRequest $request
     * @return mixed
     */
    public function register(RegisterDeviceRequest $request)
    {
        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            Device::firstOrCreate(['device_id' => $request->device_id]) // device can actually exist but have null as user
                ->user()->associate(auth()->user());
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => "credentials didn't match"]);
        }

    }
}
