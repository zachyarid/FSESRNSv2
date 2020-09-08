<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Carbon;

class LimitMassFBOPandLRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $now = Carbon::now();

        if ($request->subscription->last_pandlrequest == '0000-00-00 00:00:00')
        {
            return $next($request);
        }

        $last_request = Carbon::parse($request->subscription->last_pandlrequest);

        $difference = $now->diffInDays($last_request);

        if ($difference == 0)
        {
            return response()->json([
                'error' => true,
                'message' => 'You can request a mass FBO P/L report only once per day per subscription'
            ]);
        }

        return $next($request);
    }
}
