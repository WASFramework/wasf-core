<?php
namespace Wasf\Http\Middleware;

class StartSessionMiddleware
{
    public function handle($passable, callable $next)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $next($passable);
    }
}
