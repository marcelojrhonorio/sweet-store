<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Log;

use Closure;

class Administrator
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
        $email   = $request->session()->get('email') ?? 'empty';
        
        $authorized = [
            'smith.junior@icloud.com',
            'marcelo.campos.honorio@gmail.com',
            'bruno@sweetmedia.com.br',
            'bruno@canoadigital.com'
        ];
        
        if (false === $session->has('email') && false === $session->has('token')) {
            return redirect('/login');
        }

        if (!in_array($email, $authorized)) {
            return redirect('/login');
        }
                
        return $next($request);
    }
}
