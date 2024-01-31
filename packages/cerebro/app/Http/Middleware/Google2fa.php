<?php

namespace App\Http\Middleware;

use Closure;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Illuminate\Support\Facades\Auth;

class Google2fa
{
    public function handle($request, Closure $next)
    {
        if (env('APP_ENV') == 'local') {
            return $next($request);
        }

        $authenticator = app(Authenticator::class)->boot($request);
        $authUser = Auth::user();

        if ($authUser->google2fa_secret == null) {

            return redirect(route('generate2fa'));
        }

        if ($authenticator->isAuthenticated()) {
            return $next($request);
        }

        return $authenticator->makeRequestOneTimePasswordResponse();
    }
}
