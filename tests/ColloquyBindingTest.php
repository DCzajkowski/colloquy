<?php

namespace Tests;

use Colloquy\ColloquyBinding;
use Colloquy\Drivers\MemoryDriver;
use Colloquy\IdentifierResolverInterface;

class ColloquyBindingTest extends TestCase
{
    public function testBothGettersWorkAsExpected()
    {
        $identifierResolver = new class implements IdentifierResolverInterface {
            public function get($object): string {
                return 'sample-string-identifier';
            }
        };
        $driver = new MemoryDriver;

        $colloquyBinding = new ColloquyBinding($identifierResolver, $driver);

        $this->assertEquals($identifierResolver, $colloquyBinding->getIdentifierResolver());
        $this->assertEquals($driver, $colloquyBinding->getDriver());
    }
}
