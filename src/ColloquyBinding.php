<?php

namespace Colloquy;

use Colloquy\Drivers\DriverInterface;

class ColloquyBinding
{
    /** @type IdentifierResolverInterface */
    private $identifierResolver;

    /** @type DriverInterface */
    private $driver;

    public function __construct(IdentifierResolverInterface $identifierResolver, DriverInterface $driver)
    {
        $this->identifierResolver = $identifierResolver;
        $this->driver = $driver;
    }

    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    public function getIdentifierResolver(): IdentifierResolverInterface
    {
        return $this->identifierResolver;
    }
}
