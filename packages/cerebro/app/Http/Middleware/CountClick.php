<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Illuminate\Support\Facades\Auth;

class CountClick
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $ip = hash('sha512', $request->input('ip_address'));
        if($request->input('click_id') === 'null'){
            $request->query->remove('click_id');
        }
        if($request->input('sub_ids') === "{}"){
            $request->query->remove('sub_ids');
        }
        Visitor::find($request->input('visit_id'))->update([
            'clicks_amount' => 1
        ]);
        return $next($request);
    }
}
