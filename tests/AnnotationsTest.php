<?php

namespace Tests;

use Colloquy\Colloquy;
use Colloquy\Drivers\MemoryDriver;
use Colloquy\IdentifierResolverInterface;
use Tests\Fakes\User;
use Tests\Fakes\TestController;

class AnnotationsTest extends TestCase
{
    public function testAllPropertiesAreInjectedProperly()
    {
        Colloquy::bind('session', new class implements IdentifierResolverInterface {
            public function get($object): string {
                return 'session-id';
            }
        }, new MemoryDriver);

        $controller = new TestController;

        $this->assertNull($controller->user);

        $response = $controller->step1();

        $this->assertEquals('step1', $response);
        $this->assertInstanceOf(User::class, $controller->user);
        $this->assertEquals('John', $controller->user->getName());

        $controller->__destruct();
        $freshController = new TestController;

        $this->assertNull($freshController->user);

        $freshController->step2();

        $this->assertInstanceOf(User::class, $freshController->user);
        $this->assertEquals(21, $freshController->user->getAge());
    }
}
