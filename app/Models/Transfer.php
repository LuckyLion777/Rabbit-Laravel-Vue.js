<?php

namespace App\Models;

use App\Contracts\Notificatable;
use App\Models\Traits\Notificatable\NotificatableTransfer;
use App\Models\Traits\Transferable\Transferable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class Transfer
 * @package App\Models
 * @property Transferable transferable
 */
class Transfer extends Model implements Notificatable
{
    use NotificatableTransfer;

    protected $appends = ['breeder'];
    protected $hidden = ['transferable'];

    public function transferable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function claim(User $user)
    {
        if (!($result = $this->transferable->transfer($user))) {
            return false;
        }

        $this->accepted = true;
        $this->resolved_at = Carbon::now();
        $this->save();

        return $result;
    }

    public function decline()
    {
        $this->accepted = false;
        $this->resolved_at = Carbon::now();
        $this->save();
    }

    public function scopeActive(Builder $query)
    {
        return $query->whereNull('resolved_at');
    }

    public function scopeToBreeder(Builder $query)
    {
        $map = array_flip(Relation::morphMap());
        return $query->whereIn('transferable_type', array_map(function ($class) use ($map) {
            return @$map[$class] ?: $class;
        }, [RabbitBreeder::class, RabbitKit::class]));
    }

    public function getBreederAttribute()
    {
        return $this->transferable->likeBreeder();
    }
}
