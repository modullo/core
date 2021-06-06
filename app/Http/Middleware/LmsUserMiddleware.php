<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LmsUserMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Request
     */
    public function handle(Request $request, Closure $next)
    {
        config(['auth.guards.api.provider' => 'lms_users']);
        return $next($request);

    }

}