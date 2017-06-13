<?php

namespace App\Security;

use App\Contracts\CryptHash as CryptHashContract;

class CryptHash implements CryptHashContract
{
    private $key;

    public function __construct()
    {
        $key = config('app.key');
        if (strpos($key, 'base64:') === 0) {
            $key = base64_decode(substr($key, 7));
        }
        $this->key = $key;
    }

    public function hash($value, $algo = 'sha256')
    {
        return hash_hmac($algo, serialize($value), $this->key);
    }

    public function check($value, $hash, $algo = 'sha256')
    {
        return hash_equals($hash, static::hash($value, $algo));
    }
}
