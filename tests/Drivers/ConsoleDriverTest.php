<?php

namespace Tests\Drivers;

use Tests\TestCase;
use Colloquy\Drivers\ConsoleDriver;

class ConsoleDriverTest extends TestCase
{
    /** @var \Colloquy\Drivers\ConsoleDriver */
    private $driver;

    public function setUp()
    {
        parent::setUp();

        $this->driver = new ConsoleDriver;
    }

    public function testItPrintsSignatureOfGet()
    {
        $this->expectOutputString(sprintf("get(%s, %s)\n", $id = 'identifier', $key = 'custom-key'));

        $this->driver->get($id, $key);
    }

    public function testItPrintsSignatureOfSet()
    {
        $this->expectOutputString(vsprintf("set(%s, %s, %s)\n", [
            $id = 'identifier',
            $key = 'custom-key',
            $value = 'custom-value',
        ]));

        $this->driver->set($id, $key, $value);
    }

    public function testItPrintsSignatureOfCreate()
    {
        $this->expectOutputString(sprintf("create(%s)\n", $id = 'identifier'));

        $this->driver->create($id);
    }

    public function testItPrintsSignatureOfRemove()
    {
        $this->expectOutputString(sprintf("remove(%s)\n", $id = 'identifier'));

        $this->driver->remove($id);
    }

    public function testItPrintsSignatureOfExists()
    {
        $this->expectOutputString(sprintf("exists(%s)\n", $id = 'identifier'));

        $this->driver->exists($id);
    }
}
