<?php

namespace App\Models\Ledger;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Category $category
 */
class Entry extends Model {
    public $table = 'ledger_entries';

    public $casts = ['debit' => 'bool'];

    public function getDateAttribute($date)
    {
        return $date
                ? Carbon::createFromFormat('Y-m-d', $date)->format(\Auth::user()->getDateFormatPHP())
                : null;
    }

    public function setDateAttribute($date)
    {
        $this->attributes['date'] = $date
                                    ? Carbon::createFromFormat(\Auth::user()->getDateFormatPHP(), $date)
                                                ->toDateString()
                                    : null;
    }

    public function associated()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @param Builder $builder
     * @param bool $archived
     * @return Builder
     */
    public function scopeArchived($builder, $archived = true)
    {
        if ($archived === null) {
            return $builder;
        } elseif ($archived) {
            return $builder->where('archived_at', '<>', '');
        } else {
            return $builder->where('archived_at', null);
        }
    }

    /**
     * @param Builder $builder
     * @param User|int $user
     * @return Builder
     */
    public function scopeForUser($builder, $user)
    {
        return $builder->where('user_id', is_object($user) ? $user->id : $user);
    }

    public function getCategoryNameAttribute()
    {
        return $this->category->name;
    }
}
