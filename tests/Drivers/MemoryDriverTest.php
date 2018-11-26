<?php

namespace Tests\Drivers;

use Tests\TestCase;
use Tests\Fakes\User;
use Colloquy\Drivers\MemoryDriver;
use Colloquy\Exceptions\KeyDoesNotExistException;
use Colloquy\Exceptions\ContextDoesNotExistException;

class MemoryDriverTest extends TestCase
{
    /** @type \Colloquy\Drivers\MemoryDriver */
    private $driver;

    public function setUp()
    {
        parent::setUp();

        $this->driver = new MemoryDriver;
    }

    public function testSetThrowsAnExceptionWhenGivenContextDoesNotExist()
    {
        $this->expectException(ContextDoesNotExistException::class);

        $this->driver->set('non-existent-identifier', 'custom-string', 'custom-value');
    }

    public function testSetterAndGetterWorksAsExpected()
    {
        $this->driver->create($id = 'custom-identifier');

        $this->driver->set($id, 'custom-string', $string = 'custom-value');
        $this->driver->set($id, 'custom-integer', $integer = 20);
        $this->driver->set($id, 'custom-float', $float = 10.2);
        $this->driver->set($id, 'custom-class-instance', $instance = User::create('John', 20));

        $this->assertEquals($string, $this->driver->get($id, 'custom-string'));
        $this->assertEquals($integer, $this->driver->get($id, 'custom-integer'));
        $this->assertEquals($float, $this->driver->get($id, 'custom-float'));
        $this->assertEquals($instance, $this->driver->get($id, 'custom-class-instance'));
    }

    public function testGetThrowsAnExceptionWhenGivenContextDoesNotExist()
    {
        $this->expectException(ContextDoesNotExistException::class);

        $this->driver->get('non-existent-context', 'custom-string');
    }

    public function testGetThrowsAnExceptionWhenGivenKeyDoesNotExist()
    {
        $this->driver->create($id = 'custom-context');

        try {
            $this->expectException(KeyDoesNotExistException::class);
            $this->driver->get($id, 'non-existent-key');
        } catch (KeyDoesNotExistException $e) {
            $this->driver->remove($id = 'custom-context');
            throw $e;
        }
    }

    public function testExistsReturnsABooleanRegardingContextsExistence()
    {
        $this->driver->create($id = 'custom-context');

        $this->assertTrue($this->driver->exists($id));

        $this->driver->remove($id = 'custom-context');

        $this->assertFalse($this->driver->exists($id));
    }
}
