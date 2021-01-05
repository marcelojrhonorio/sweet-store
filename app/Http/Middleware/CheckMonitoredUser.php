<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class CheckMonitoredUser
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

        if (env('MONITOR_USERS')) {
            $customerEmail = $session->get('email') ?? 'default';
            $fullUrl       = $request->fullUrl()    ?? 'default';
    
            if (self::isMonitoredUser($customerEmail)) {
                Log::debug('Usuário monitorado de e-mail ' . $customerEmail . ' acessou a rota ' . $fullUrl);
            }
        }

        return $next($request);
    }

    private static function isMonitoredUser (string $email)
    {
        $monitoredUsers = [
            'marcelo.campos.honorio@gmail.com',
            'Regina17mariene@gmail.com',
            'regina17mariene@gmail.com',
            'elielsono40@gmail.com'
        ];

        if (in_array($email, $monitoredUsers)) {
            return true;
        }

        return false;
    }

}
