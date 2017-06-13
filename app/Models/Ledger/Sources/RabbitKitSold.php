<?php

namespace App\Models\Ledger\Sources;

use App\Models\Ledger\Category as LedgerCategory;
use App\Models\RabbitKit;

final class RabbitKitSold extends EntrySource
{
    const LEDGER_SOURCE_TYPE = 'sold:kit';

    /**
     * @var RabbitKit
     */
    private $kit;

    /**
     * @param RabbitKit $kit
     */
    public function __construct($kit)
    {
        $this->kit = $kit;
    }

    protected function getEventType()
    {
        return self::LEDGER_SOURCE_TYPE;
    }

    protected function getEventId()
    {
        return $this->kit->getKey();
    }

    protected function getName()
    {
        return "Sold Kit {$this->kit->given_id} from Litter {$this->kit->litter->given_id}";
    }

    protected function getCategory()
    {
        return LedgerCategory::where('special', 'litter')->first();
    }

    protected function getAssociated()
    {
        return $this->kit->litter;
    }
}
