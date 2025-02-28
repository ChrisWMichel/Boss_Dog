<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('AdminMiddleware: Checking admin status');
        if(Auth::user() && Auth::user()->is_admin == 1) {
            return $next($request);
        } else {
            return response([
                'message' => "You don't have permission to access this page.",
            ], 403);
        }
       
    }
}
