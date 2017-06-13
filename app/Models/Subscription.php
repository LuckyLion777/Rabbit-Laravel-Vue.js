<?php

namespace App\Models;

class Subscription extends \Stripe\Subscription
{
    public static function all($params = null, $options = null)
    {
        return parent::_all($params, $options);
    }
}
