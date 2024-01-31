<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Illuminate\Support\Facades\Auth;

class CountVisitor
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
        $visitor = Visitor::create(
            [
                'ip_address' => $ip,
                'click_id' => $request->input('click_id'),
                'referral_id' => $request->user()->id,
                'date' => today(),
                'flow_id' => $request->input('flow_id'),
                'user_agent' => $request->input('user_agent'),
                'url' => $request->input('url'),
                'visits_amount' => 1,
                'clicks_amount' => $request->input('click'),
                'sub_ids'=> json_decode($request->input('sub_ids'), true, JSON_THROW_ON_ERROR),
                'ref_url' => $request->input('ref_url'),
                'action_type' => $request->input('click') ? 'full' : null,
                'cookie_mark' => $request->input('cookie_mark') ?? false
            ]
        );
        $request->visit_id = $visitor->id;
        return $next($request);
    }
}
