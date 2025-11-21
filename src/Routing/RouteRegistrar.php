<?php
namespace Wasf\Routing;

class RouteRegistrar
{
    protected RouteCollection $collection;
    protected string $method;
    protected string $uri;

    public function __construct(RouteCollection $collection, string $method, string $uri)
    {
        $this->collection = $collection;
        $this->method = $method;
        $this->uri = $uri;
    }

    public function name(string $name): self
    {
        $this->collection->name($this->method, $this->uri, $name);
        return $this;
    }

    public function middleware(string|array $middleware): self
    {
        $this->collection->addMiddlewareFor($this->method, $this->uri, (array)$middleware);
        return $this;
    }
}
