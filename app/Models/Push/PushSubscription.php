<?php

namespace App\Models\Push;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    protected $fillable = ['endpoint', 'server_public_key', 'client_public_key', 'auth_token'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
