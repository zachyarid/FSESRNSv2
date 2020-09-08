<?php

namespace App\Http\Middleware;

use Closure;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $role
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $role = \App\Role::where('name', 'Administrator')->first();

        if (!$request->user()->roles->contains($role->id)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
