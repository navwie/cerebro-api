<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class SsnCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $tooManyAttempts = RateLimiter::tooManyAttempts(
            config('dnm.ssnMiddleware.prefixKey').$request->input('ip_address'),
            config('dnm.ssnMiddleware.maxAttempts')
        );
        if ($tooManyAttempts) {
            return response()->json(['message' => 'Try it later.'], 429);
        }
        return $next($request);
    }
}
