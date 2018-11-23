<?php

namespace Colloquy;

use BadMethodCallException;

trait ColloquyContexts
{
    public function __call(string $method, array $arguments)
    {
        if (!method_exists($this, $method)) {
            throw new BadMethodCallException(vsprintf('Call to undefined method %s::%s()', [
                get_class($this),
                $method,
            ]));
        }

        ColloquyAnnotations::handle($this, $method);

        return $this->{$method}(...$arguments);
    }

    public function __destruct()
    {
        ColloquyAnnotations::persist($this);
    }
}
