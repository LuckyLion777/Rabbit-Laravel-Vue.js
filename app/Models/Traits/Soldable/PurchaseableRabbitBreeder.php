<?php

namespace App\Models\Traits\Soldable;

use App\Models\Ledger\Sources\RabbitBreederBought;

trait PurchaseableRabbitBreeder
{
    public function purchasedLedgerSource($from = null)
    {
        return new RabbitBreederBought($this, $from);
    }
}
