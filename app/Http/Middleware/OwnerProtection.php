<?php

namespace App\Http\Middleware;

use Closure;

class OwnerProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $level = 2, $entityName = null)
    {
        $entityName = $entityName ?: $request->segment($level);
        $user = auth()->user();
        /* @var $user \App\Models\User */
        if (!$user || !$user->hasRole('admin') && $request->route($entityName)->user_id != $user->id) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
