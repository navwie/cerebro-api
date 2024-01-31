<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\v1\ReapplyController;
use App\Models\CustomerState;
use Closure;
use Illuminate\Http\Request;

class SearchReapply
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
        $response = $next($request);

        if(isset($response->original['status']) && $response->original['status'] == 'not found'){
            ReapplyController::save_customer_state($request);
        }

        return $response;
    }
}
