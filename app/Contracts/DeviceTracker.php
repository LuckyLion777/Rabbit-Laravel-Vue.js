<?php

namespace App\Contracts;

use App\Models\Device;
use App\Models\User;

/**
 * Stores/reads deviceId for current session
 */
interface DeviceTracker
{
    /**
     * Sets deviceId for current session.
     * Should probably be called at the beginning of the session.
     *
     * @param string $deviceId
     */
    public function setDeviceId($deviceId);

    /**
     * Associated device with ID currently in session (if any) with specified user.
     *
     * @param User $user
     */
    public function associateWithUser($user);

    /**
     * Breaks connection between the device and user. Call this when user logs out
     * if you no longer want to associate current device ID with this user.
     */
    public function forgetUser();

    /**
     * Gets device model corresponding to the device ID set in current session, if any.
     *
     * @return Device|null
     */
    public function getDevice();
}
