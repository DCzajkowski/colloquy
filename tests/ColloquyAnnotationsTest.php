<?php

namespace Tests;

use Tests\Fakes\User;
use Colloquy\Colloquy;
use Tests\Fakes\TestController;
use Colloquy\Drivers\MemoryDriver;
use Colloquy\IdentifierResolverInterface;
use Colloquy\Exceptions\ContextNotDefinedException;
use Tests\Fakes\TestControllerWithoutContextDeclaration;
use Tests\Fakes\TestControllerWithInvalidContextDeclaration;

class ColloquyAnnotationsTest extends TestCase
{
    public function testAllPropertiesAreInjectedProperly()
    {
        Colloquy::bind('session', new class implements IdentifierResolverInterface {
            public function get($object): string {
                return 'session-id';
            }
        }, $driver = new MemoryDriver);

        $controller = new TestController;

        $this->assertNull($controller->user);

        $response = $controller->step1();

        $this->assertTrue($driver->exists('session-id'));
        $this->assertEquals('step1', $response);
        $this->assertInstanceOf(User::class, $controller->user);
        $this->assertEquals('John', $controller->user->getName());

        $controller = new TestController;

        $this->assertEquals([], $driver->get('session-id', 'custom-identifier-form'));

        $this->assertNull($controller->user);

        $controller->step2();

        $this->assertInstanceOf(User::class, $controller->user);
        $this->assertEquals('John', $controller->user->getName());
        $this->assertEquals(21, $controller->user->getAge());

        $controller = new TestController;

        $user = $controller->step3();

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->getName());
        $this->assertEquals(21, $user->getAge());

        $controller->__destruct();

        $this->assertFalse($driver->exists('session-id'));
    }

    public function testExceptionIsThrownWhenThereIsNoColloquyContextAnnotation()
    {
        $this->expectException(ContextNotDefinedException::class);

        $controller = new TestControllerWithoutContextDeclaration;

        try {
            $controller->step1();
        } catch (ContextNotDefinedException $e) {
            //
        }
    }

    public function testExceptionIsThrownWhenThereIsNoColloquyContextBinding()
    {
        $this->expectException(ContextNotDefinedException::class);

        $controller = new TestControllerWithInvalidContextDeclaration;

        try {
            $controller->step1();
        } catch (ContextNotDefinedException $e) {
            //
        }
    }
}
