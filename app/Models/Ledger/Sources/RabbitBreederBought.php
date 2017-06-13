<?php

namespace App\Models\Ledger\Sources;

use App\Models\RabbitBreeder;
use App\Models\Ledger\Category as LedgerCategory;

class RabbitBreederBought extends EntrySource
{
    const LEDGER_SOURCE_TYPE = 'bought:breeder';

    /**
     * @var RabbitBreeder
     */
    private $breeder;

    /**
     * @var string|null
     */
    private $from;

    public function __construct($breeder, $from = null)
    {
        $this->breeder = $breeder;
        $this->from = $from;
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
        return "Purchased {$this->breeder->name}: {$this->breeder->tattoo}";
    }

    protected function getCategory()
    {
        return LedgerCategory::where('special', 'breeder')->first();
    }

    protected function getAssociated()
    {
        return $this->breeder;
    }
    
    protected function getDescription()
    {
        return $this->from ? 'Purchased from ' . $this->from : null;
    }
}
