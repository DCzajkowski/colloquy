<?php

namespace Colloquy;

use Colloquy\Drivers\DriverInterface;
use Colloquy\Exceptions\ContextAlreadyExistsException;
use Colloquy\Exceptions\UserDefinedContextNotFoundException;

class Colloquy
{
    public const PREFIX = 'Colloquy';
    protected static $bindings = [];
    protected $driver;

    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public static function getBoundContext(string $contextName, object $object): ColloquyContext
    {
        $binding = self::$bindings[$contextName];

        return new ColloquyContext($binding['identifierResolver']->get($object), new self($binding['driver']));
    }

    public static function bind($contextName, IdentifierResolverInterface $identifierResolver, DriverInterface $driver)
    {
        self::$bindings[$contextName] = [
            'identifierResolver' => $identifierResolver,
            'driver' => $driver,
        ];
    }

    public static function makeSelfFromBinding(string $contextName)
    {
        return new self(self::$bindings[$contextName]['driver']);
    }

    public static function doesContextBindingExist(string $contextName): bool
    {
        return array_key_exists($contextName, self::$bindings);
    }

    public function contextExists(string $contextName, object $object): bool
    {
        return $this->driver->exists(self::$bindings[$contextName]['identifierResolver']->get($object));
    }

    public static function createContextFromBinding(string $contextName, object $object)
    {
        if (!Colloquy::doesContextBindingExist($contextName)) {
            throw new UserDefinedContextNotFoundException('Context ' . $contextName . ' not found in user-defined bindings. Have you called Colloquy::bind?');
        }

        $binding = self::$bindings[$contextName];
        $colloquy = new self($binding['driver']);
        $colloquy->begin($binding['identifierResolver']->get($object));
    }

    public function begin(string $identifier): ColloquyContext
    {
        if ($this->driver->exists($identifier)) {
            throw new ContextAlreadyExistsException;
        }

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
