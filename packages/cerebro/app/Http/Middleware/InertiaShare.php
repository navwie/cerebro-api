<?php

namespace App\Http\Middleware;

use Closure;
use Inertia\Inertia;

class InertiaShare
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
        Inertia::share([
            'app.name' => config('app.name'),
            'user' => function () {
                return auth()->user() ? auth()->user()->only('id', 'name', 'email', 'roles') : null;
            },
        ]);

        return $next($request);
    }
}