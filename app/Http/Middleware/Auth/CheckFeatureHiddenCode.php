<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class CheckFeatureHiddenCode
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
        $featureCode = env('FEATURE_HIDDEN_CODE');
        if (is_null($featureCode) || ($featureCode !== $request->get('code'))) {
            throw new UnauthorizedException('Error code');
        }
        
        return $next($request);
    }
}
