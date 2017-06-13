<?php

namespace App\Models\Traits\Soldable;

use App\Models\Ledger\Sources\RabbitKitSold;

trait SoldableRabbitKit
{
    public function soldLedgerSource()
    {
        return new RabbitKitSold($this);
    }
}
