<?php

namespace App\Http\Middleware;

use App\Subscription;
use App\SubscriptionStatus;
use Closure;

class CheckSubscriptionStatus
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
        $sS = SubscriptionStatus::all();

        foreach ($sS as $s)
        {
            if ($request->subscription->status == $s->id)
            {
                if ($s->negative_effect)
                {
                    abort($s->error_code, $s->status_message);
                }
            }
        }
        return $next($request);
    }
}
