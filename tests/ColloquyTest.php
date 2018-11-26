<?php

namespace Tests;

use Colloquy\Colloquy;
use Colloquy\ColloquyContext;
use Colloquy\Drivers\MemoryDriver;
use Colloquy\Exceptions\ContextAlreadyExistsException;
use Colloquy\Exceptions\UserDefinedContextNotFoundException;

class ColloquyTest extends TestCase
{
    public function testBeginCreatesAContextAndReturnsIt()
    {
        $wrapper = new Colloquy($driver = new MemoryDriver);

        $context = $wrapper->begin($identifier = 'identifier');

        $this->assertTrue($driver->exists($identifier));
        $this->assertFalse($driver->exists('non-existent-identifier'));
        $this->assertInstanceOf(ColloquyContext::class, $context);
        $this->assertEquals($identifier, $context->getIdentifier());
    }

    public function testBeginThrowsAnExceptionIfContextWithSpecifiedIdentifierAlreadyExists()
    {
        $this->expectException(ContextAlreadyExistsException::class);

        $driver = new MemoryDriver;
        $driver->create($identifier = 'identifier');

        $wrapper = new Colloquy($driver);

        $wrapper->begin($identifier);
    }

    public function testContextMethodCreatesANewContextWhenItDoesNotExistAndReturnsIt()
    {
        $wrapper = new Colloquy($driver = new MemoryDriver);

        $context = $wrapper->context($identifier = 'identifier');

        $this->assertTrue($driver->exists($identifier));
        $this->assertFalse($driver->exists('non-existent-identifier'));
        $this->assertInstanceOf(ColloquyContext::class, $context);
        $this->assertEquals($identifier, $context->getIdentifier());
    }

    public function testContextMethodReturnsAContextIfItExists()
    {
        $driver = new MemoryDriver;
        $driver->create($identifier = 'identifier');

        $wrapper = new Colloquy($driver);

        $context = $wrapper->context($identifier);

        $this->assertTrue($driver->exists($identifier));
        $this->assertFalse($driver->exists('non-existent-identifier'));
        $this->assertInstanceOf(ColloquyContext::class, $context);
        $this->assertEquals($identifier, $context->getIdentifier());
    }

    public function testEndRemovesAContext()
    {
        $wrapper = new Colloquy($driver = new MemoryDriver);
        $wrapper->begin($identifier = 'identifier');

        $this->assertTrue($driver->exists($identifier));

        $wrapper->end($identifier);

        $this->assertFalse($driver->exists($identifier));
    }

    public function testGetDriverReturnsTheDriver()
    {
        $wrapper = new Colloquy($driver = new MemoryDriver);

        $this->assertEquals($driver, $wrapper->getDriver());
    }

    public function testThrowsAnExceptionWhenContextBindingDoesNotExist()
    {
        $this->expectException(UserDefinedContextNotFoundException::class);

        Colloquy::createContextFromBinding('non-existent-context', new class {});
    }
}
