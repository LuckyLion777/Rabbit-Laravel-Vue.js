<?php

namespace App\Pdf;

use App\Pdf\Contracts\Mpdf;
use Illuminate\Support\Facades\Facade;

class MpdfFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return Mpdf::class;
    }
}
