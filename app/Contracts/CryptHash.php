<?php

namespace App\Contracts;

interface CryptHash
{
    public function hash($value);

    public function check($value, $hash);
}
