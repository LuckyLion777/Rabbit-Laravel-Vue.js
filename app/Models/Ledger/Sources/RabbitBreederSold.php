<?php

namespace App\Models\Ledger\Sources;

use App\Models\Ledger\Category as LedgerCategory;
use App\Models\RabbitBreeder;

final class RabbitBreederSold extends EntrySource
{
    const LEDGER_SOURCE_TYPE = 'sold:breeder';

    /**
     * @var RabbitBreeder
     */
    private $breeder;

    /**
     * @param RabbitBreeder $breeder
     */
    public function __construct($breeder)
    {
        $this->breeder = $breeder;
    }

    protected function getEventType()
    {
        return self::LEDGER_SOURCE_TYPE;
    }
    
    protected function getEventId()
    {
        return $this->breeder->getKey();
    }

    protected function getName()
    {
        return "Sold Breeder {$this->breeder->name}";
    }

    protected function getCategory()
    {
        return LedgerCategory::where('special', 'breeder')->first();
    }

    protected function getAssociated()
    {
        return $this->breeder;
    }
}
