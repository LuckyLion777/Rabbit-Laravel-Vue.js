<?php

namespace App\Models\Ledger;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Entry[]|Collection $entries
 * @property Entry[]|Collection $myEntries
 * @property User $user
 */
class Category extends Model
{
    const CATEGORY_GENERAL = 'general';
    const CATEGORY_BREEDER = 'breeder';
    const CATEGORY_LITTER = 'litter';

    protected $table = 'ledger_categories';

    protected $appends = ['count'];

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function myEntries()
    {
        return $this->entries()->where('user_id', auth()->id());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCountAttribute()
    {
        return $this->myEntries->count();
    }

    /**
     * Users are not allowed to modify the default (locked) categories
     * @return bool
     */
    public function isLocked()
    {
        return !!$this->special;
    }
}
