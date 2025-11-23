<?php
namespace Wasf\Http\Middleware;

use Wasf\Auth\AuthManager;
use Wasf\Http\Response;

class AuthMiddleware
{
    public function handle($request, $next)
    {
        if (!AuthManager::instance()->check()) {
            return redirect('/login')->with('error', 'Silahkan login terlebih dahulu');
        }

        return $next($request);
    }
}
