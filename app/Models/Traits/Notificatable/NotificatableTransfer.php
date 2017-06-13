<?php

namespace App\Models\Traits\Notificatable;

use App\Models\Transfer;

trait NotificatableTransfer
{
    use Notificatable;

    public static function actualNotifications()
    {
        $user = \Auth::user();
        /* @var $user \App\Models\User */
        return $user->allTransfers()->active()->toBreeder()->with('transferable')->get();
    }
}
