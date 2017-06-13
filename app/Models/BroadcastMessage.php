<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BroadcastMessage extends Model
{
    protected $table = 'broadcast';
    
    protected $fillable = ['title', 'content'];

    /**
     * @return static
     */
    public static function active()
    {
        $current = static::orderby('id', 'DESC')->first();
        return $current && !$current->disabled_at ? $current : null;
    }

    public function deactivate()
    {
        $this->disabled_at = Carbon::now();
        $this->save();
    }

    public function isDismissed($guard = null)
    {
        $user = \Auth::guard($guard)->user();
        if (!$user) {
            return \Session::get('broadcast_dismissed_' . $this->id, false);
        }

        return \DB::table('broadcast_dismissed')->where('broadcast_id', $this->id)
                    ->where('user_id', $user->id)->exists();
    }

    public function dismiss($guard = null)
    {
        $user = \Auth::guard($guard)->user();
        if (!$user) {
            \Session::set('broadcast_dismissed_' . $this->id, true);
            return;
        }

        \DB::table('broadcast_dismissed')->insert([
            'broadcast_id' => $this->id,
            'user_id' => $user->id,
        ]);
    }
}
