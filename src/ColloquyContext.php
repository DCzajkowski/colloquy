<?php

namespace Colloquy;

use Colloquy\Exceptions\KeyNotProvidedException;

class ColloquyContext
{
    /** @type string */
    protected $identifier;

    /** @type Colloquy */
    protected $wrapper;

    public function __construct(string $identifier, Colloquy $wrapper)
    {
        $this->identifier = $identifier;
        $this->wrapper = $wrapper;
    }

    public function add($data, string $key = null): void
    {
        if ($key === null && is_object($data)) {
            $key = get_class($data);
        } else if ($key === null) {
            throw new KeyNotProvidedException;
        }

        $this->wrapper->getDriver()->set($this->identifier, $key, $data);
    }

    public function get(string $key)
    {
        return $this->wrapper->getDriver()->get($this->identifier, $key);
    }

    public function set($value, string $key): void
    {
        $this->wrapper->getDriver()->set($this->identifier, $key, $value);
    }

    public function end(): void
    {
        $this->wrapper->getDriver()->remove($this->identifier);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
