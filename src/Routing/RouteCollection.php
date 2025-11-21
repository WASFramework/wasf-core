<?php
namespace Wasf\Routing;

class RouteCollection
{
    protected array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
    ];

    protected array $namedRoutes = [];

    public function add(string $method, string $uri, array $action)
    {
        $route = [
            'uri'        => trim($uri, '/'),
            'action'     => $action,
            'middleware' => [],
            'name'       => null,
            'wheres'     => [],
            'params'     => [], // parsed params hasil match
        ];

        return $this->routes[$method][] = $route;
    }

    public function name(string $method, string $uri, string $name)
    {
        $key = strtoupper($method) . ':' . trim($uri, '/');
        $this->namedRoutes[$name] = $key;
    }

    public function getNamedRoutes(): array
    {
        return $this->namedRoutes;
    }

    public function loadFromDump(array $dump)
    {
        if (isset($dump['routes'])) $this->routes = $dump['routes'];
        if (isset($dump['named'])) $this->namedRoutes = $dump['named'];
    }

    public function generate(string $name, array $params = []): ?string
    {
        $map = $this->namedRoutes;
        if (!isset($map[$name])) return null;

        // stored value: METHOD:uri
        $entry = $map[$name];
        [$method, $uri] = explode(':', $entry, 2);

        // replace placeholders
        $url = $uri;
        foreach ($params as $k => $v) {
            $url = preg_replace('/\{'.preg_quote($k, '/').'(\:[^\}]+)?\??\}/', $v, $url, 1);
        }

        // remove any optional segments left (e.g. /{slug?})
        $url = preg_replace('#\{[^}]+\?\}#', '', $url);
        // collapse multiple slashes
        $url = preg_replace('#//+#', '/', $url);
        return '/' . trim($url, '/');
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function match(string $method, string $uri)
    {
        $method = strtoupper($method);
        $uri = trim($uri, '/');

        foreach ($this->routes[$method] as $i => $route) {
            if ($this->matchUri($route, $uri, $params)) {
                // simpan parameter ke route
                $this->routes[$method][$i]['params'] = $params;
                return $this->routes[$method][$i];
            }
        }

        return null;
    }

    /**
     * MATCHING ADVANCED PARAMETER
     * - {id}
     * - {name?}
     * - {slug:[a-z]+}
     */
    protected function matchUri(array $route, string $uri, &$params): bool
    {
        $params = [];

        $pattern = preg_replace_callback('/\{([^\/]+)\}/', function ($m) use ($route, &$params) {

            $segment = $m[1];

            // Optional: {name?}
            if (str_ends_with($segment, '?')) {
                $key = rtrim($segment, '?');
                $params[$key] = null;
                return '(?:([^\/]+))?';
            }

            // Regex: {slug:[a-z]+}
            if (str_contains($segment, ':')) {
                [$key, $regex] = explode(':', $segment, 2);
                $params[$key] = null;
                return '(' . $regex . ')';
            }

            // Normal param: {id}
            $params[$segment] = null;
            return '([^\/]+)';
        }, $route['uri']);

        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches);
            $keys = array_keys($params);
            $params = array_combine($keys, $matches);
            return true;
        }

        return false;
    }

    public function addMiddlewareFor(string $method, string $uri, array $middleware)
    {
        $method = strtoupper($method);
        foreach ($this->routes[$method] as $i => $r) {
            if ($r['uri'] === trim($uri, '/')) {
                $this->routes[$method][$i]['middleware'] = array_merge($r['middleware'] ?? [], $middleware);
                return true;
            }
        }
        return false;
    }
}
