<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property RabbitBreeder[]|Collection $entries
 * @property RabbitBreeder[]|Collection $myEntries
 * @property User $user
 */
class RabbitBreederCategory extends Model
{
    const CATEGORY_GENERAL = 'general';

    protected $table = 'rabbit_breeder_categories';

    protected $appends = ['count'];

    public function entries()
    {
        return $this->hasMany(RabbitBreeder::class, 'category_id');
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
        return $this->myEntries()->count();
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
