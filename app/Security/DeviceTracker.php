<?php

namespace App\Security;

use App\Contracts\DeviceTracker as DeviceTrackerContract;
use App\Models\Device;
use Illuminate\Session\Store;

class DeviceTracker implements DeviceTrackerContract
{
    const SESSION_KEY = '_device_id';

    /**
     * @var string
     */
    private $deviceId;

    /**
     * null means not loaded, false - device does not exist
     *
     * @var null|false|Device
     */
    private $device;

    /**
     * @var Store
     */
    private $session;

    public function __construct(Store $session)
    {
        $this->session = $session;
        $this->deviceId = $session->get(static::SESSION_KEY);
        $this->device = null;
    }

    public function __destruct()
    {
        $this->ensureDevice();
        $this->flushToSession();
    }

    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
        $this->flushToSession();
    }

    public function associateWithUser($user)
    {
        if (!($device = $this->getDevice())) {
            return;
        }

        $device->user()->associate($user)->save();
    }

    public function forgetUser()
    {
        if (!($device = $this->getDevice()) || !$device->user_id) {
            return;
        }

        $device->user()->associate(null)->save();
    }

    public function getDevice()
    {
        if ($this->device === null) {
            $this->device = Device::query()->where('device_id', '=', $this->deviceId)
                ->first() ?: false;
        }
        return $this->device ?: null;
    }

    private function ensureDevice()
    {
        if ($this->getDevice() !== null) {
            return;
        }

        $this->device = Device::create([ 'device_id' => $this->deviceId ]);
        $this->device->fresh();
    }

    private function flushToSession()
    {
        $this->session->set(static::SESSION_KEY, $this->deviceId);
    }
}
