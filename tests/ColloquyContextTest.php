<?php

namespace Tests;

use Mockery;
use Tests\Fakes\User;
use Colloquy\Colloquy;
use Colloquy\ColloquyContext;
use Colloquy\Drivers\MemoryDriver;
use Colloquy\Drivers\DriverInterface;
use Colloquy\Exceptions\KeyNotProvidedException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class ColloquyContextTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testGetIdentifierReturnsACorrectIdentifier()
    {
        $context = new ColloquyContext($id = 'custom-identifier', new Colloquy(new MemoryDriver()));

        $this->assertEquals($id, $context->getIdentifier());
    }

    public function testAddCallsSetOnTheDriverWithCorrectIdentifierAndKeyAndValue()
    {
        $driver = Mockery::spy(DriverInterface::class);

        $context = new ColloquyContext($id = 'custom-identifier', new Colloquy($driver));

        $context->add($data = 'custom-string-value', $key = 'custom-key');

        $driver->shouldHaveReceived('set')
            ->with($id, $key, $data)
            ->once();
    }

    public function testAddCallsSetOnTheDriverWithCorrectIdentifierAndKeyAndValueWhenIdentifierIsNotProvided()
    {
        $driver = Mockery::spy(DriverInterface::class);

        $context = new ColloquyContext($id = 'custom-identifier', new Colloquy($driver));

        $context->add($data = User::create('Jane', 21));

        $driver->shouldHaveReceived('set')
            ->with($id, User::class, $data)
            ->once();
    }

    public function testAddThrowsAnExceptionIfKeyWasNotProvidedAndValueIsNotAnObject()
    {
        $this->expectException(KeyNotProvidedException::class);

        $context = new ColloquyContext($id = 'custom-identifier', new Colloquy(new MemoryDriver));

        $context->add(10);
    }

    public function testGetReturnsCorrectValue()
    {
        $colloquy = new Colloquy(new MemoryDriver);
        $context = $colloquy->context($id = 'custom-identifier');

        $context->add($data = 'custom-string-value', $key = 'custom-key');

        $this->assertEquals($data, $context->get($key));
    }

    public function testSetSetsACorrectValue()
    {
        $colloquy = new Colloquy(new MemoryDriver);
        $context = $colloquy->context($id = 'custom-identifier');

        $context->add($data = 'custom-string-value', $key = 'custom-key');

        $this->assertEquals($data, $context->get($key));

        $context->set($newData = 'new-custom-string-value', $key);

        $this->assertEquals($newData, $context->get($key));
    }
}
