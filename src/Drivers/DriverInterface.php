<?php

namespace Colloquy\Drivers;

interface DriverInterface
{
    /**
     * Retrieves a single value from the context.
     *
     * @param string $id  ID of the context
     * @param string $key Key to retrieve the value by
     * @return mixed
     */
    public function get(string $id, string $key);

    /**
     * Sets a single value in the context.
     *
     * @param string $id    ID of the context
     * @param string $key   Key to retrieve the value by
     * @param mixed  $value Value to be put
     */
    public function set(string $id, string $key, $value): void;

    /**
     * Creates a context with given ID.
     *
     * @param string $id ID of a context
     */
    public function create(string $id): void;

    /**
     * Removes a context with given ID.
     *
     * @param string $id ID of a context
     */
    public function remove(string $id): void;

    /**
     * Checks if a context with given ID exists.
     *
     * @param string $id ID of a context
     * @return bool
     */
    public function exists(string $id): bool;
}
