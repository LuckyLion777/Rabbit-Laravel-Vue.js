<?php

namespace App\Models\Traits\Soldable;

use App\Models\Ledger\Sources\RabbitBreederSold;

trait SoldableRabbitBreeder
{
    public function soldLedgerSource()
    {
        return new RabbitBreederSold($this);
    }
}
