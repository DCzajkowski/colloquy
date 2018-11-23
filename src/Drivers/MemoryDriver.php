<?php

namespace Colloquy\Drivers;

class MemoryDriver implements DriverInterface
{
    private $contexts = [];

    public function get(string $id, string $key)
    {
        return $this->contexts[$id][$key];
    }

    public function set(string $id, string $key, $value): void
    {
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
}
