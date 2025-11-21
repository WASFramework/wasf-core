<?php
namespace Wasf\Routing;

use Wasf\Http\Pipeline;
use Wasf\Foundation\Application;

class Dispatcher
{
    protected RouteCollection $collection;
    protected Application $app;
    protected Router $router;

    public function __construct(RouteCollection $collection, Application $app, Router $router)
    {
        $this->collection = $collection;
        $this->app = $app;
        $this->router = $router;
    }

    public function dispatchRoute(array $route)
    {
        $action = $route['action']['uses'] ?? $route['action'] ?? null;
        $params = $route['params'] ?? [];

        // 1. global middleware
        $middleware = $this->router->getGlobalMiddleware();

        // 2. route middleware (already on route array)
        $routeMiddleware = $route['middleware'] ?? [];
        $middleware = array_merge($middleware, $routeMiddleware);

        // 3. expand middleware groups and aliases into class names
        $expanded = $this->expandMiddlewareList($middleware);

        // 4. Build passable object (you can use Request object if present)
        $passable = [
            'params' => $params,
            'route'  => $route,
        ];

        // 5. Execute pipeline
        $pipeline = new Pipeline();

        return $pipeline->send($passable)
            ->through($expanded)
            ->then(function ($pass) use ($action) {
                return $this->callAction($action, $pass['params']);
            });
    }

protected function expandMiddlewareList(array $list): array
{
    $expanded = [];

    foreach ($list as $m) {

        // 1. Middleware Group
        if (is_string($m) && $this->router->getMiddlewareGroup($m)) {
            foreach ($this->router->getMiddlewareGroup($m) as $g) {
                $expanded = array_merge($expanded, $this->expandMiddlewareList((array)$g));
            }
            continue;
        }

        // 2. Alias → Class
        if (is_string($m) && $this->router->getMiddlewareAlias($m)) {
            $expanded[] = $this->router->getMiddlewareAlias($m);
            continue;
        }

        // 3. Class Middleware
        if (is_string($m) && class_exists($m)) {
            $expanded[] = $m;
            continue;
        }

        // 4. Closure Middleware
        if (is_callable($m)) {
            $expanded[] = $m;
            continue;
        }

        if (!is_callable($m) 
            && !class_exists($m) 
            && !$this->router->getMiddlewareAlias($m) 
            && !$this->router->getMiddlewareGroup($m)) 
        {
            continue;
        }

        // 5. Invalid middleware → IGNORE (FIX)
        // skip invalid string like 'start_session_mw'
    }

    return $expanded;
}


protected function callAction($action, array $params)
{
    // Closure route
    if ($action instanceof \Closure) {
        return $action(...array_values($params));
    }

    // Controller@method route
    if (is_string($action) && str_contains($action, '@')) {

        [$controller, $method] = explode('@', $action, 2);

        // 1. Fully Qualified Controller
        if (str_contains($controller, '\\')) {
            $fqcn = $controller;
        } else {

            $fqcn = null;

            /**
             * -------------------------------------------------
             *   FIND HMVC CONTROLLER
             * -------------------------------------------------
             */
            $basePath = $this->app->basePath(); // posisi: wasf-core/src/Routing → 2x naik ke wasf-app/

            $hmvcPaths = [
                $basePath . "/Modules/*/Controllers/{$controller}.php",
                $basePath . "/Modules/*/Controllers/{$controller}.php"
            ];

            foreach ($hmvcPaths as $pattern) {
                foreach (glob($pattern) as $file) {
                    $module = basename(dirname(dirname($file))); // folder modul
                    $fqcn = "Modules\\{$module}\\Controllers\\{$controller}";
                    break 2;
                }
            }

            /**
             * -------------------------------------------------
             *   FALLBACK TO MVC CONTROLLER
             * -------------------------------------------------
             */
            if (!$fqcn) {
                $fqcn = "App\\Controllers\\{$controller}";
            }
        }

        // If still not found
        if (!class_exists($fqcn)) {
            throw new \Exception("Controller {$fqcn} not found");
        }

        // Instantiate controller
        $instance = $this->app->make($fqcn);

        if (!method_exists($instance, $method)) {
            throw new \Exception("Method {$method} not found in {$fqcn}");
        }

        $ref = new \ReflectionMethod($fqcn, $method);

        // Build argument list with DI + model binding
        $args = [];

        foreach ($ref->getParameters() as $p) {

            $name = $p->getName();
            $type = $p->getType();

            // Object dependency injection
            if ($type && !$type->isBuiltin()) {
                $class = $type->getName();

                // Model binding
                if (class_exists($class) && method_exists($class, 'find') && isset($params[$name])) {
                    $args[] = $class::find($params[$name]);
                    continue;
                }

                // Inject Request, Response, Services, etc
                $args[] = $this->app->make($class);
                continue;
            }

            // Route param
            if (isset($params[$name])) {
                $args[] = $params[$name];
                continue;
            }

            // Default param
            if ($p->isDefaultValueAvailable()) {
                $args[] = $p->getDefaultValue();
                continue;
            }

            $args[] = null;
        }

        return $ref->invokeArgs($instance, $args);
    }

    throw new \Exception("Unsupported route action");
}

}
