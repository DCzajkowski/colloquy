<?php

namespace Colloquy\Drivers;

use Colloquy\Exceptions\KeyDoesNotExistException;
use Colloquy\Exceptions\ContextDoesNotExistException;
use Colloquy\Exceptions\ContextCreationFailedException;

class FileDriver implements DriverInterface
{
    protected $path;

    public function __construct(string $path = '')
    {
        $this->path = rtrim($path, DIRECTORY_SEPARATOR);
    }

    public function get(string $id, string $key)
    {
        $fileName = $this->ensureContextExists($id);

        $contents = json_decode(file_get_contents($fileName), true);

        if (!$contents || !key_exists($key, $contents)) {
            throw new KeyDoesNotExistException($id, $key);
        }

        return unserialize($contents[$key]);
    }

    public function set(string $id, string $key, $value): void
    {
        $fileName = $this->ensureContextExists($id);

        $contents = json_decode(file_get_contents($fileName), true);

        $contents[$key] = serialize($value);

        file_put_contents($fileName, json_encode($contents));
    }

    public function create(string $id): void
    {
        $this->createDirectoryIfDoesNotExist();

        $fileName = $this->getFileName($id);

        if (!is_writeable($this->path) || (is_file($fileName) && !is_writeable($this->getFileName($id)))) {
            throw new ContextCreationFailedException($id, $fileName);
        }

        file_put_contents($fileName, '');
    }

    public function remove(string $id): void
    {
        $fileName = $this->ensureContextExists($id);

        unlink($fileName);
    }

    public function exists(string $id): bool
    {
        return file_exists($this->getFileName($id));
    }

    protected function getFileName(string $id): string
    {
        return $this->path . DIRECTORY_SEPARATOR . $id . '.txt';
    }

    protected function createDirectoryIfDoesNotExist(): void
    {
        if (!is_dir($path = $this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    protected function ensureContextExists(string $id): string
    {
        if (!is_file($fileName = $this->getFileName($id))) {
            throw new ContextDoesNotExistException;
        }

        return $fileName;
    }
}
