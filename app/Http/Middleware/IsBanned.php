<?php

namespace App\Http\Middleware;

use App\Services\Response\ResponseService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IsBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {

//        if($request->user()->blocked >= Carbon::now()) {
//            return ResponseService::sendJsonResponse(
//                false,
//                423,
//                [],
//                []
//            );
//        }
        return $next($request);
    }
}
