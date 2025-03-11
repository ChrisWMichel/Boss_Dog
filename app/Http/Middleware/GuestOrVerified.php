<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;

class GuestOrVerified extends EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse  
     */
    public function handle($request, Closure $next, $redirectToRoute = null): Response
    {
        if(!$request->user()){
            return $next($request);
        }
        return parent::handle($request, $next, $redirectToRoute);
    }
}
