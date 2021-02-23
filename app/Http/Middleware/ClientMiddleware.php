<?php


namespace App\Http\Middleware;

use Closure;
class ClientMiddleware
{



  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param  string|null  $guard
   * @return mixed
   */
  public function handle($request, Closure $next, $guard = null)
  {
    if ( validate_api_client($request)) {
      return $next($request);

    }

  }

}