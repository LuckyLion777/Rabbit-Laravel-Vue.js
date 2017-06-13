<?php

namespace App\Repositories;

use App\Models\Ledger\Entry;

class LedgerEntryRepository extends Repository
{
    protected $createFromFields = ['name', 'date', 'category_id', 'debit', 'amount', 'description', 'user_id', 'associated_id', 'associated_type'];
    protected $updateFromFields = ['name', 'date', 'category_id', 'debit', 'amount', 'description', 'associated_id', 'associated_type'];

    /**
     * LodgerEntryRepository constructor.
     * @param Entry $entry
     */
    public function __construct(Entry $entry)
    {
        $this->object = $entry;
    }
}
