<?php

namespace App\Http\Middleware;

use Closure;

class Logged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $session = $request->session();

        if (false === $session->has('email') && false === $session->has('token')) {
            return redirect('/login');
        }

        return $next($request);
    }
}
