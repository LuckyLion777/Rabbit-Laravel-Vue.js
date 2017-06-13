<?php

namespace App\Push\Web;

use App\Models\Push\PushSubscription;
use App\Models\User;
use App\Push\Contracts\Pusher as PusherContract;
use App\Push\Message;
use Minishlink\WebPush\WebPush;

class Pusher implements PusherContract
{
    private $defaultOptions;

    private $webPusher;

    public function __construct ($defaultOptions = [])
    {
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function sendToUser(User $user, Message $message)
    {
        $subscriptions = $user->pushSubscriptions()
            ->where('server_public_key', config('services.web_push.public'))
            ->get();

        if (!($sent = $subscriptions->count())) {
            return 0;
        }

        foreach ($subscriptions as $subscription) {
            /* @var $subscription PushSubscription */
            $this->sendToSubscription($subscription, $message);
        }

        if (is_array($res = $this->flush())) {
            \Log::error('Some push notifications failed: ' . print_r($res, true));
            $sent -= $this->processFailures($res);
        }

        return $sent;
    }

    /**
     * Register a new (or update existing) endpoint to some user so that we can send them messages later
     * @param User $user
     * @param array $credentials
     * @param bool $overrideServerPublic
     * @return PushSubscription
     */
    public function register(User $user, $credentials, $overrideServerPublic)
    {
        $subscription = PushSubscription::firstOrNew(['endpoint' => $credentials['endpoint']]);
        /* @var $subscription PushSubscription */
        $subscription->fill($credentials);
        \Log::info('Web Push register: ' . ($overrideServerPublic ? '(RESET) ' : '') . print_r($credentials, true));
        if ($overrideServerPublic || !$subscription->exists) {
            $subscription->server_public_key = config('services.web_push.public');
        }
        return $user->pushSubscriptions()->save($subscription);
    }

    protected function sendToSubscription(PushSubscription $subscription, Message $message, $flush = false)
    {
        return $this->getWebPusher()->sendNotification(
            $subscription->endpoint,
            json_encode($message->getAllOptions(), JSON_UNESCAPED_UNICODE),
            $subscription->client_public_key,
            $subscription->auth_token,
            $flush,
            [],
            [
                'VAPID' => [
                    'subject' => 'https://htch.us',
                    'publicKey' => $subscription->server_public_key,
                    'privateKey' => config('services.web_push.private'),
                ]
            ]
        );
    }

    protected function flush()
    {
        return $this->getWebPusher()->flush();
    }

    protected function getWebPusher()
    {
        if (!$this->webPusher) {
            $this->webPusher = new WebPush([], $this->defaultOptions);
        }
        return $this->webPusher;
    }

    private function processFailures($failures)
    {
        $processed = 0;
        foreach ($failures as $failure) {
            if ($failure['success']) continue;
            if ($failure['expired']) $this->forget($failure['endpoint']);
            $processed += 1;
        }
        return $processed;
    }

    private function forget($endpoint)
    {
        PushSubscription::where('endpoint', $endpoint)->delete();
    }
}
