<?php

namespace App\Push;

use App\Models\User;
use App\Push\Contracts\Pusher as PusherContract;

/**
 * This pusher just unifies a number of pushers
 */
class Pusher implements PusherContract
{
    private $pushers;

    /**
     * Pusher constructor.
     * @param PusherContract[] $pushers
     */
    public function __construct($pushers)
    {
        $this->pushers = $pushers;
    }

    /**
     * {@inheritdoc}
     */
    public function sendToUser(User $user, Message $message)
    {
        $total = 0;
        foreach ($this->pushers as $pusher) {
            $total += $pusher->sendToUser($user, $message);
        }
        return $total;
    }
}
