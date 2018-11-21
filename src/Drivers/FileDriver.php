<?php

namespace Colloquy\Drivers;

class FileDriver implements DriverInterface
{
    protected $path;

    public function __construct(string $path = '')
    {
        $this->path = $path;
    }

    public function get(string $id, string $key)
    {
        $contents = json_decode(file_get_contents($this->getFileName($id)), true);

        return unserialize($contents[$key]);
    }

    public function set(string $id, string $key, $value): void
    {
        $contents = json_decode(file_get_contents($this->getFileName($id)), true);

        $contents[$key] = serialize($value);

        file_put_contents($this->getFileName($id), json_encode($contents));
    }

    public function create(string $id): void
    {
        file_put_contents($this->getFileName($id), '');
    }

    public function remove(string $id): void
    {
        unlink($this->getFileName($id));
    }

    public function exists(string $id): bool
    {
        return file_exists($this->getFileName($id));
    }

    protected function getFileName(string $id): string
    {
        return $this->path . '/' . $id . '.txt';
    }
}
