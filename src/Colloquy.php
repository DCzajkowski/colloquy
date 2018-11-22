<?php

namespace Colloquy;

use Colloquy\Drivers\DriverInterface;

class Colloquy
{
    protected $driver;

    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function begin(string $identifier): ColloquyContext
    {
        $this->driver->create($identifier);

        return new ColloquyContext($identifier, $this);
    }

    public function context(string $identifier): ColloquyContext
    {
        if (!$this->driver->exists($identifier)) {
            return $this->begin($identifier);
        }

        return new ColloquyContext($identifier, $this);
    }

    public function end(string $identifier): void
    {
        $this->driver->remove($identifier);
    }

    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }
}
