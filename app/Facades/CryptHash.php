<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CryptHash extends Facade
{
    public static function getFacadeAccessor()
    {
        return \App\Contracts\CryptHash::class;
    }
}
