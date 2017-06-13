<?php

namespace App\Push\Contracts;

use App\Models\User;
use App\Push\Message;

/**
 * Service able to send notifications to users
 */
interface Pusher
{
    /**
     * Send a message to user
     * @param User $user
     * @param Message $message
     * @return int Number of channels engaged
     */
    public function sendToUser(User $user, Message $message);
}
