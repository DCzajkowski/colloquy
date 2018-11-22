<?php

namespace Tests\Drivers;

use Tests\TestCase;
use Colloquy\Drivers\FileDriver;

class FileDriverTest extends TestCase
{
    /** @var \Colloquy\Drivers\FileDriver */
    private $driver;

    public function setUp()
    {
        parent::setUp();

        $this->driver = new FileDriver();
    }

    public function testGetReturnsCorrectKeysValue()
    {
        // @TODO
    }

    public function testGetThrowsAnExceptionWhenGivenContextDoesNotExist()
    {
        // @TODO
    }

    public function testGetThrowsAnExceptionWhenGivenKeyDoesNotExist()
    {
        // @TODO
    }

    public function testSetSetsValueOfGivenKey()
    {
        // @TODO
    }

    public function testSetThrowsAnExceptionWhenGivenContextDoesNotExist()
    {
        // @TODO
    }

    public function testItCreatesAContextFile()
    {
        // @TODO
    }

    public function testCreateThrowsAnExceptionIfContextCreationFailed()
    {
        // @TODO
    }

    public function testItRemovesAFileWithGivenContext()
    {
        // @TODO
    }

    public function testRemoveThrowsAnExceptionIfTheContextFileDoesNotExist()
    {
        // @TODO
    }

    public function testExistsReturnsABooleanRegardingFilesExistence()
    {
        // @TODO
    }
}
