<?php
namespace Wasf\Http\Middleware;

class GuestMiddleware
{
    public function handle(array $params, \Closure $next)
    {
        if (auth()->check()) {
            // prevent logged in user from visiting login/register
            return redirect('/profile')->send();
        }
        return $next($params);
    }
}
