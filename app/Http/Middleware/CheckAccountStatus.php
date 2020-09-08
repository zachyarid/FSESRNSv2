<?php

namespace App\Http\Middleware;

use App\UserStatus;
use Closure;

class CheckAccountStatus
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
        $user = $request->user();

        $uS = UserStatus::all();

        foreach ($uS as $s)
        {
            if ($user->status == $s->id)
            {
                if ($s->negative_effect)
                {
                    abort($s->error_code, $s->status_message);
                } else if ($s->negative_effect == 0 && $s->id !== 1)
                {
                    $request->session()->flash('warning_message', $s->status_message);
                }
            }
        }

        return $next($request);
    }
}
