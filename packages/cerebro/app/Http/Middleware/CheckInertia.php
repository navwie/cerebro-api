<?php

namespace App\Http\Middleware;

use Closure;
use Inertia\Inertia;

class CheckInertia
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
        if ($request->session()->get('use_inertia') == 1) {
            Inertia::setRootView('app');
        }


        return $next($request);
    }
}