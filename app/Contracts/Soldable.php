<?php

namespace App\Contracts;

use App\Models\Ledger\Sources\EntrySource;

interface Soldable
{
    /**
     * @return EntrySource
     */
    public function soldLedgerSource();
}
