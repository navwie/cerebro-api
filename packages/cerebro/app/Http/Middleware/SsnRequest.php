<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\SsnRequests as SsnRequestsModel;

class SsnRequest
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
        $key = config('dnm.ssnMiddleware.prefixKey').$request->input('ip_address');
        $delay_lvl = SsnRequestsModel::getDelay($request);
        $decay_seconds = config('dnm.ssnMiddleware.decaySeconds') * $delay_lvl;
        $executed = RateLimiter::attempt(
            $key,
            config('dnm.ssnMiddleware.maxAttempts'),
            function() {

            },
            $decay_seconds
        );
        if (! $executed) {
            return response()->json(['message' => 'Too many attempts!'], 429);
        }
        if(RateLimiter::attempts($key)==config('dnm.ssnMiddleware.maxAttempts')){
            SsnRequestsModel::increacDelayLvl($request);
        }
        return $next($request);
    }
}
