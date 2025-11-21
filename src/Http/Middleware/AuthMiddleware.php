<?php
namespace Wasf\Middleware;

class AuthMiddleware
{
    public function handle(array $params, \Closure $next)
    {
        if (!auth()->check()) {
            // store intended url (optional)
            $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? '/';
            return redirect('/login')->with('error', 'Please login to access this page')->send();
        }
        return $next($params);
    }
}
