<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next, $level = 'basic')
    {
        if ($this->shouldCheck($request) ) {
            $user = $request->user();
            /* @var $user User */
            if (!($level === 'basic' ? $user->isSubscribed() : $user->isPremium())) {
                return abort(403);
            }
        }
        return $next($request);
    }
    
    protected function shouldCheck(Request $request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return false;
            }
        }

        return true;
    }
    
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'admin/users/*',
    ];
}
