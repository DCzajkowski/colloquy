<?php

namespace Colloquy\Drivers;

class ConsoleDriver implements DriverInterface
{
    public function get(string $id, string $key)
    {
        echo 'get(', $key, ')' . PHP_EOL;
    }

    public function set(string $id, string $key, $value): void
    {
        echo 'set(', $key, ', ', $value, ')' . PHP_EOL;
    }

    public function create(string $id): void
    {
        echo 'create(', $id, ')' . PHP_EOL;
    }

    public function remove(string $id): void
    {
        echo 'remove(', $id, ')' . PHP_EOL;
    }

    public function exists(string $id): bool
    {
        echo 'exists(', $id, ')' . PHP_EOL;
    }
}
