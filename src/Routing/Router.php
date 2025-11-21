<?php
namespace Wasf\Routing;

class Router
{
    protected RouteCollection $collection;
    protected array $groupStack = [];

    // middleware aliases (alias => class)
    protected array $middlewareAliases = [];

    // middleware groups (name => [middleware,...])
    protected array $middlewareGroups = [];

    // global middleware list (ordered)
    protected array $globalMiddleware = [];

    public function __construct()
    {
        $this->collection = new RouteCollection();
    }

    /**
     * Register GET/POST
     */
    public function get(string $uri, $action) { return $this->addRoute('GET', $uri, $action); }
    public function post(string $uri, $action) { return $this->addRoute('POST', $uri, $action); }

    public function addRoute(string $method, string $uri, $action)
    {
        $attributes = $this->mergeGroupAttributes([
            'prefix' => '',
            'middleware' => [],
        ]);

        if ($attributes['prefix']) {
            $uri = trim($attributes['prefix'], '/') . '/' . trim($uri, '/');
        }

        $data = ['uses' => $action];

        $route = $this->collection->add($method, $uri, $data);

        if (!empty($attributes['middleware'])) {
            // add middleware to collection for this route
            $this->collection->addMiddlewareFor($method, $uri, (array)$attributes['middleware']);
        }

        return new RouteRegistrar($this->collection, $method, $uri);
    }

    public function group(array $attr, \Closure $callback)
    {
        $this->groupStack[] = $attr;
        $callback($this);
        array_pop($this->groupStack);
    }

    protected function mergeGroupAttributes(array $route)
    {
        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $route['prefix'] = trim($group['prefix'] . '/' . ($route['prefix'] ?? ''), '/');
            }
            if (isset($group['middleware'])) {
                $route['middleware'] = array_merge($route['middleware'] ?? [], (array)$group['middleware']);
            }
        }
        return $route;
    }

    public function dispatch(string $method, string $uri)
    {
        return $this->collection->match($method, $uri);
    }

    public function route(string $name): ?string
    {
        $map = $this->collection->getNamedRoutes();
        return $map[$name] ?? null;
    }

    // ---------------- Middleware API ----------------

    /** Register an alias for middleware */
    public function aliasMiddleware(string $alias, string $class): void
    {
        $this->middlewareAliases[$alias] = $class;
    }

    public function getMiddlewareAlias(string $alias): ?string
    {
        return $this->middlewareAliases[$alias] ?? null;
    }

    /** Register a middleware group (e.g. 'web' => [...]) */
    public function middlewareGroup(string $name, array $list): void
    {
        $this->middlewareGroups[$name] = $list;
    }

    public function getMiddlewareGroup(string $name): ?array
    {
        return $this->middlewareGroups[$name] ?? null;
    }

    /** Register global middleware (applies to all routes) */
    public function middleware(array $list): void
    {
        $this->globalMiddleware = array_merge($this->globalMiddleware, $list);
    }

    public function getGlobalMiddleware(): array
    {
        return $this->globalMiddleware;
    }

    // expose collection for helpers / CLI
    public function getCollection(): RouteCollection
    {
        return $this->collection;
    }
}
