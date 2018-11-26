<?php

namespace Tests\Drivers;

use Tests\TestCase;
use Tests\Fakes\User;
use Colloquy\Drivers\FileDriver;
use Colloquy\Exceptions\KeyDoesNotExistException;
use Colloquy\Exceptions\ContextDoesNotExistException;
use Colloquy\Exceptions\ContextCreationFailedException;

class FileDriverTest extends TestCase
{
    /** @type \Colloquy\Drivers\FileDriver */
    private $driver;

    public function setUp()
    {
        parent::setUp();

        $this->driver = new FileDriver($this->dir());
    }

    private function dir(string $path = '')
    {
        return __DIR__ . '/../out' . ($path ? '/' . $path : '');
    }

    public function testItCreatesAContextFile()
    {
        $this->driver->create($id = 'custom-identifier');

        $this->assertFileExists($file = $this->dir($id . '.txt'));

        unlink($file);
    }

    public function testItCreatesSubdirectoriesIfTheyDoNotExist()
    {
        $this->assertDirectoryNotExists($dir = $this->dir('non-existent/sub-folder'));

        $driver = new FileDriver($dir);
        $driver->create('new-identifier');

        $this->assertDirectoryExists($dir = $this->dir('non-existent/sub-folder'));

        unlink($this->dir('non-existent/sub-folder/new-identifier.txt'));
        rmdir($this->dir('non-existent/sub-folder'));
        rmdir($this->dir('non-existent'));
    }

    public function testCreateThrowsAnExceptionIfContextCreationFailed()
    {
        $driver = new FileDriver($this->dir('protected'));

        if (!is_dir($dir = $this->dir('protected'))) {
            mkdir($dir, 0000); // this will create permanently a directory, that will be hard to delete.
        }

        try {
            $this->expectException(ContextCreationFailedException::class);

            $driver->create($id = 'custom-identifier');
        } catch (ContextCreationFailedException $e) {
            rmdir($dir);

            throw $e;
        }
    }

    public function testItRemovesAFileWithGivenContext()
    {
        $this->driver->create($id = 'custom-identifier');

        $this->assertFileExists($this->dir($id . '.txt'));

        $this->driver->remove($id);

        $this->assertFileNotExists($this->dir($id . '.txt'));
    }

    public function testRemoveThrowsAnExceptionIfTheContextFileDoesNotExist()
    {
        $this->expectException(ContextDoesNotExistException::class);

        $this->driver->remove('custom-identifier');
    }

    public function testSetSetsValueOfGivenKey()
    {
        $this->driver->create($id = 'custom-identifier');

        $this->driver->set($id, 'custom-string', $string = 'custom-value');
        $this->driver->set($id, 'custom-integer', $integer = 20);
        $this->driver->set($id, 'custom-float', $float = 10.2);
        $this->driver->set($id, 'custom-class-instance', $instance = User::create('John', 20));

        $fileContents = file_get_contents($this->dir($id . '.txt'));

        $this->assertJsonStringEqualsJsonString(json_encode([
            'custom-string' => serialize($string),
            'custom-integer' => serialize($integer),
            'custom-float' => serialize($float),
            'custom-class-instance' => serialize($instance),
        ]), $fileContents);

        $this->driver->remove($id);
    }

    public function testSetThrowsAnExceptionWhenGivenContextDoesNotExist()
    {
        $this->expectException(ContextDoesNotExistException::class);

        $this->driver->set('non-existent-identifier', 'custom-string', 'custom-value');
    }

    public function testGetReturnsCorrectKeysValue()
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

        $this->driver->remove($id);
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

    public function testExistsReturnsABooleanRegardingFilesExistence()
    {
        $this->driver->create($id = 'custom-context');

        $this->assertTrue($this->driver->exists($id));

        $this->driver->remove($id = 'custom-context');

        $this->assertFalse($this->driver->exists($id));
    }
}
