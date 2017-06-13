<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class InitTrial
{
    public function handle(Request $request, Closure $next)
    {
        if ($user = $request->user()) {
            /* @var $user \App\Models\User */
            if ($user->trial_ends_at === null) {
                $user->trial_ends_at = Carbon::now()->addDay(10);
                $user->save();
            }
        }
        return $next($request);
    }
}
