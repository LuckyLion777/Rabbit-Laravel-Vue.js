<?php

namespace App\Contracts;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface Notificatable
{
    /**
     * @return Notificatable[]|Collection
     */
    public static function actualNotifications();

    /**
     * @param User $user
     * @return Notification
     */
    public function getNotification(User $user);
}
