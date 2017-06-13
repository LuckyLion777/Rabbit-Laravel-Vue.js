<?php

namespace App\Models\Traits\Transferable;

use App\Models\Pedigree;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Message;

/**
 * Entity which can be transferred from one user account to another
 * @package App\Models\Traits\Transferable
 */
trait Transferable
{
    /**
     * @param User $target
     * @return Transfer\TransferResult
     */
    abstract public function transfer(User $target);

    /**
     * @return string
     */
    abstract protected function getType();

    /**
     * @return string
     */
    abstract protected function getTypePlural();

    public function transfers()
    {
        /* @var $this Model */
        return $this->morphMany(Transfer::class, 'transferable');
    }

    /**
     * First step of the transfer process. The user receives a message inviting them to claim a record
     * @param User|string $target User object or email
     */
    public function initiateTransfer($target)
    {
        $user = $this->resolveTargetUser($target);

        $transfer = new Transfer();
        if ($user) {
            $transfer->user()->associate($user);
        } else {
            $transfer->user_email = $target;
        }
        $this->transfers()->save($transfer);

        \Mail::send('emails.transfer', [
            'type' => $this->getType(),
            'type_plural' => $this->getTypePlural(),
            'source_user' => \Auth::user(),
            'target_user' => $user,
            'url' => route('admin.transfer', ['transfer' => $transfer->getKey()])
        ], function(Message $message) use ($user, $target) {
            $message->to($user ? $user->email : $target);
            $message->subject(ucfirst($this->getType()) . ' transfer');
        });
    }

    /**
     * @param User|string $target
     * @return User|null
     */
    protected function resolveTargetUser($target)
    {
        if (is_object($target)) {
            return $target;
        }
        return User::whereEmail($target)->first();
    }

    /**
     * @return Pedigree[]
     */
    protected function transferablePedigree()
    {
        $pedigree_number_generations = $this->user->pedigree_number_generations;
        $this->user->pedigree_number_generations = 4;
        $pedigree = $this->pedigree();
        $this->user->pedigree_number_generations = $pedigree_number_generations;
        return $pedigree;
    }
}
