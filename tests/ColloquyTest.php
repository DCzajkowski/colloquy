<?php

namespace Tests;

use Colloquy\Colloquy;
use Colloquy\ColloquyContext;
use Tests\Drivers\FakeDriver;

class ColloquyTest extends TestCase
{
    public function testBeginCreatesAContextAndReturnsIt()
    {
        $wrapper = new Colloquy($driver = new FakeDriver);

        $context = $wrapper->begin($identifier = 'identifier');

        $this->assertTrue($driver->exists($identifier));
        $this->assertFalse($driver->exists('non-existent-identifier'));
        $this->assertInstanceOf(ColloquyContext::class, $context);
        $this->assertEquals($identifier, $context->getIdentifier());
    }

    public function testContextMethodCreatesANewContextWhenItDoesNotExistAndReturnsIt()
    {
        $wrapper = new Colloquy($driver = new FakeDriver);

        $context = $wrapper->context($identifier = 'identifier');

        $this->assertTrue($driver->exists($identifier));
        $this->assertFalse($driver->exists('non-existent-identifier'));
        $this->assertInstanceOf(ColloquyContext::class, $context);
        $this->assertEquals($identifier, $context->getIdentifier());
    }

    public function testContextMethodReturnsAContextIfItExists()
    {
        $driver = new FakeDriver;
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
        $wrapper = new Colloquy($driver = new FakeDriver);
        $wrapper->begin($identifier = 'identifier');

        $this->assertTrue($driver->exists($identifier));

        $wrapper->end($identifier);

        $this->assertFalse($driver->exists($identifier));
    }

    public function testGetDriverReturnsTheDriver()
    {
        $wrapper = new Colloquy($driver = new FakeDriver);

        $this->assertEquals($driver, $wrapper->getDriver());
    }
}
