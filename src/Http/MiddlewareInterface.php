<?php
namespace Wasf\Http;
interface MiddlewareInterface
{
    public function handle(Request $request, Response $response, callable $next);
}
