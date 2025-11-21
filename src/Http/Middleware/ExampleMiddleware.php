<?php
namespace Wasf\Http\Middleware;

use Wasf\Http\Request;
use Wasf\Http\Response;

class ExampleMiddleware
{
    public function handle(Request $request, Response $response, callable $next)
    {
        $response->header('X-WASF', '1');
        return $next($request, $response);
    }
}
