<?php

namespace App\Models\Traits\Notificatable;

trait NotificatableEvent
{
    use Notificatable;

    public static function actualNotifications()
    {
        return \Auth::user()->upcomingEvents;
    }
}
