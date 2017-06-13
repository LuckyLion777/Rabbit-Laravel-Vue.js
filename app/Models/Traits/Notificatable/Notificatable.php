<?php

namespace App\Models\Traits\Notificatable;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @property Notification notification
 */
trait Notificatable
{
    public function getNotification(User $user)
    {
        /* @var $this \Illuminate\Database\Eloquent\Model */
        if ($user->id == \Auth::id() && $this->notification) {
            return $this->notification;
        }

        $aliases = array_flip(Relation::morphMap());
        $alias = isset($aliases[get_called_class()]) ? $aliases[get_called_class()] : get_called_class();

        $notification = Notification::where([
            [ 'object_type', $alias ],
            [ 'object_id', $this->getKey() ],
            [ 'user_id', $user->id ],
        ])->first();
        if ($notification) {
            return $notification;
        }

        $notification = Notification::forceCreate([
            'object_type' => $alias,
            'object_id' => $this->getKey(),
            'user_id' => $user->id
        ]);
        return $notification;
    }

    public function notification()
    {
        /* @var $this \Illuminate\Database\Eloquent\Model */
        return $this->morphOne(Notification::class, 'object')->where('user_id', \Auth::id());
    }
}
