<?php

namespace Colloquy\Drivers;

use Colloquy\Exceptions\KeyDoesNotExistException;
use Colloquy\Exceptions\ContextDoesNotExistException;

class MemoryDriver implements DriverInterface
{
    private $contexts = [];

    public function get(string $id, string $key)
    {
        $this->ensureContextExists($id);

        if (!key_exists($key, $this->contexts[$id])) {
            throw new KeyDoesNotExistException($id, $key);
        }

        return $this->contexts[$id][$key];
    }

    public function set(string $id, string $key, $value): void
    {
        $this->ensureContextExists($id);

        $this->contexts[$id][$key] = $value;
    }

    public function create(string $id): void
    {
        $this->contexts[$id] = [];
    }

    public function remove(string $id): void
    {
        unset($this->contexts[$id]);
    }

    public function exists(string $id): bool
    {
        return array_key_exists($id, $this->contexts);
    }

    protected function ensureContextExists(string $id): void
    {
        if (!key_exists($id, $this->contexts)) {
            throw new ContextDoesNotExistException;
        }
    }
}
