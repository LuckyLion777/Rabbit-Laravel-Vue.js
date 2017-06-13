<?php

namespace App\Models\Ledger\Sources;

use App\Models\Ledger\Category as LedgerCategory;
use App\Models\Ledger\Entry as LedgerEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class provides way to created and remove ledger entries corresponding to some events.
 * @package App\Models\Ledger\Sources
 */
abstract class EntrySource
{
    /**
     * "Type" of event (e.g. rabbit got sold). Must be unique in pair with id
     * @return string
     */
    abstract protected function getEventType();

    /**
     * Identifier to be unique in pair with type
     * @return int
     */
    abstract protected function getEventId();

    /**
     * Name for the ledger entry (for user)
     * @return string
     */
    abstract protected function getName();

    /**
     * Category for the ledger entry
     * @return LedgerCategory
     */
    abstract protected function getCategory();

    /**
     * Model it association for the ledger entry required
     * @return Model|null
     */
    abstract protected function getAssociated();

    /**
     * Date for the ledger entry
     * @return string
     */
    protected function getDate()
    {
        return Carbon::now()->format(\Auth::user()->getDateFormatPHP());
    }

    /**
     * Description for the ledger entry
     * @return string
     */
    protected function getDescription()
    {
        return null;
    }

    /**
     * Create a new ledger entry from the source
     * @param int $amount
     * @param bool $debit
     * @param User $user
     * @return LedgerEntry|false
     */
    final public function save($amount, $debit = true, $user = null)
    {
        if ($user === null) {
            $user = \Request::user();
        }

        $entry = new LedgerEntry();
        $entry->name = $this->getName();
        $entry->date = $this->getDate();
        $entry->category()->associate($this->getCategory());
        $entry->debit = $debit;
        $entry->amount = $amount;
        $entry->description = $this->getDescription();
        if ($associated = $this->getAssociated()) {
            $entry->associated()->associate($this->getAssociated());
        }

        $entry->source_event = $this->getEventType();
        $entry->source_id = $this->getEventId();

        return $user->ledger()->save($entry);
    }

    /**
     * @param User $user (false to search among all users)
     * @return LedgerEntry|null
     */
    final public function find($user = null)
    {
        if ($user === null) {
            $user = \Request::user();
        }
        $ledger = $user === false ? LedgerEntry::query() : $user->ledger();

        return $ledger->where([
            'source_event' => $this->getEventType(),
            'source_id' => $this->getEventId(),
        ])->first();
    }

    /**
     * Attempts to find and delete record made from this source (if any)
     * @param User $user (false to search among all users)
     * @return bool
     */
    final public function delete($user = null)
    {
        if ($entry = $this->find($user)) {
            return $entry->delete();
        }

        return false;
    }
}
