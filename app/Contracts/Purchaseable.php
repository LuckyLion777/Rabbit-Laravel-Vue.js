<?php

namespace App\Contracts;

use App\Models\Ledger\Sources\EntrySource;

interface Purchaseable
{
    /**
     * @param string|null $from
     * @return EntrySource
     */
    public function purchasedLedgerSource($from = null);
}
