<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Notificatable;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationReadRequest;
use App\Http\Requests\NotificationsSeenRequest;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = array_map(function ($class) {
            /* @var $class Notificatable */
            return $class::actualNotifications()->load('notification')->map(function (Notificatable $item) {
                return $item->getNotification(\Auth::user())->getObjectData();
            });
        }, [
            'events' => Event::class,
            'transfers' => Transfer::class,
        ]);

        return $request->wantsJson()
                ? response()->json($notifications)
                : response()->view('notifications.index', compact('notifications'));
    }

    public function read(Notification $notification, NotificationReadRequest $request)
    {
        $notification->read_at = $request->input('read') ? Carbon::now() : null;
        $notification->save();
        return response()->json([]);
    }

    public function seen(NotificationsSeenRequest $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->whereIn('id', $request->input('ids', []))->whereNull('seen_at')
            ->update(['seen_at' => Carbon::now()]);
        return response()->json([]);
    }
}
