<?php
namespace Wasf\Http;

class Pipeline
{
    protected array $pipes = [];
    protected $passable;

    public function send($passable): self
    {
        $this->passable = $passable;
        return $this;
    }

    public function through(array $pipes): self
    {
        $this->pipes = $pipes;
        return $this;
    }

    public function then(callable $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            function ($next, $pipe) {
                return function ($passable) use ($next, $pipe) {
                    // $pipe can be:
                    // - fully qualified class name (string) -> instantiate via container? we'll new here
                    // - callable middleware function ($passable, $next)
                    if (is_string($pipe) && class_exists($pipe)) {
                        $obj = new $pipe();
                        if (method_exists($obj, 'handle')) {
                            return $obj->handle($passable, $next);
                        }
                        return $next($passable);
                    } elseif (is_callable($pipe)) {
                        return $pipe($passable, $next);
                    } else {
                        return $next($passable);
                    }
                };
            },
            function ($passable) use ($destination) {
                return $destination($passable);
            }
        );

        return $pipeline($this->passable);
    }
}
