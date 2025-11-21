<?php
namespace Wasf\Foundation;
class Application
{
    protected string $basePath;
    protected array $bindings = [];
    protected array $config = [];
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    }
    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
    }
    public function bind(string $abstract, $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }
    public function make(string $class)
    {
        if (isset($this->bindings[$class])) {
            $concrete = $this->bindings[$class];
            if (is_callable($concrete)) return $concrete($this);
            if (is_string($concrete)) $class = $concrete;
        }

        $reflect = new \ReflectionClass($class);
        if (!$reflect->isInstantiable()) throw new \Exception("Class {$class} is not instantiable");
        $ctor = $reflect->getConstructor();
        if (!$ctor) return $reflect->newInstance();

        $params = [];
        foreach ($ctor->getParameters() as $p) {
            $type = $p->getType();
            if ($type && !$type->isBuiltin()) {
                $params[] = $this->make($type->getName());
            } elseif ($p->isDefaultValueAvailable()) {
                $params[] = $p->getDefaultValue();
            } else {
                $params[] = null;
            }
        }
        return $reflect->newInstanceArgs($params);
    }
    public function config(array $config = []){
        $this->config = array_merge($this->config, $config);
    }
    public function getConfig(){
        return $this->config;
    }
}
