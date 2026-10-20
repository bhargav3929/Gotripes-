<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession as BaseAuthenticateSession;

/**
 * Same as the framework middleware (a session is tied to the password it was
 * opened with), but a session that is signed out after a password change is
 * sent to the login page it came from, on the host it was on. The framework
 * sends everyone to route('login') on APP_URL, which for a tenant manager is
 * the customer login on the wrong domain.
 */
class AuthenticateSession extends BaseAuthenticateSession
{
    protected function redirectTo(Request $request)
    {
        $path = $request->is('manager', 'manager/*') ? '/manager/login' : '/login';

        return $request->getSchemeAndHttpHost() . $path;
    }
}
