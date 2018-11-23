<?php

namespace Tests;

use Colloquy\Support\AnnotationsParser;
use ReflectionClass;
use ReflectionException;
use Tests\Fakes\TestClass;

class AnnotationsParserTest extends TestCase
{
    protected $testClass;

    public function setUp()
    {
        parent::setUp();

        $this->testClass = new TestClass;
    }

    public function testItReadsCorrectlyClassAnnotations()
    {
        $this->assertEquals([
            'package' => 'Tests\Fakes',
            'ColloquyContext' => 'strategy',
        ], AnnotationsParser::getClassAnnotation($this->testClass));
    }

    public function testItReadsCorrectlyPropertyAnnotation()
    {
        $this->assertEquals([
            'var' => 'string A variable holding a name #uselessComment',
        ], AnnotationsParser::getPropertyAnnotation($this->testClass, 'name'));

        $this->assertEquals([
            'type' => 'int',
        ], AnnotationsParser::getPropertyAnnotation($this->testClass, 'age'));

        $this->assertEquals([
            'ColloquyPersist' => '',
        ], AnnotationsParser::getPropertyAnnotation($this->testClass, 'height'));

        $this->assertEquals([
            'ColloquyPersist' => 'user-weight',
        ], AnnotationsParser::getPropertyAnnotation($this->testClass, 'weight'));

        $this->assertEquals([
            'ColloquyPersist' => '',
            'type' => 'int',
        ], AnnotationsParser::getPropertyAnnotation($this->testClass, 'wage'));
    }

    public function testPropertyAnnotationTagExistsReturnsCorrectValues()
    {
        $this->assertFalse(AnnotationsParser::propertyAnnotationTagExists($this->testClass, 'age', 'ColloquyPersist'));
        $this->assertFalse(AnnotationsParser::propertyAnnotationTagExists($this->testClass, 'name', 'ColloquyPersist'));

        $this->assertTrue(AnnotationsParser::propertyAnnotationTagExists($this->testClass, 'height', 'ColloquyPersist'));
        $this->assertTrue(AnnotationsParser::propertyAnnotationTagExists($this->testClass, 'weight', 'ColloquyPersist'));
        $this->assertTrue(AnnotationsParser::propertyAnnotationTagExists($this->testClass, 'wage', 'ColloquyPersist'));
    }

    public function testPropertyAnnotationTagValueReturnsCorrectValues()
    {
        $this->assertEquals('user-weight', AnnotationsParser::propertyAnnotationTagValue($this->testClass, 'weight', 'ColloquyPersist'));
        $this->assertEquals('', AnnotationsParser::propertyAnnotationTagValue($this->testClass, 'height', 'ColloquyPersist'));
    }

    /** @throws ReflectionException */
    public function testGetAnnotationFromReflectionPropertyReturnsCorrectValues()
    {
        $reflection = new ReflectionClass($this->testClass);

        $this->assertEquals([
            'var' => 'string A variable holding a name #uselessComment',
        ], AnnotationsParser::getAnnotationFromReflectionProperty($reflection->getProperty('name')));

        $this->assertEquals([
            'type' => 'int',
        ], AnnotationsParser::getAnnotationFromReflectionProperty($reflection->getProperty('age')));

        $this->assertEquals([
            'ColloquyPersist' => '',
        ], AnnotationsParser::getAnnotationFromReflectionProperty($reflection->getProperty('height')));

        $this->assertEquals([
            'ColloquyPersist' => 'user-weight',
        ], AnnotationsParser::getAnnotationFromReflectionProperty($reflection->getProperty('weight')));

        $this->assertEquals([
            'ColloquyPersist' => '',
            'type' => 'int',
        ], AnnotationsParser::getAnnotationFromReflectionProperty($reflection->getProperty('wage')));
    }

    public function testGetPropertyValueReturnsCorrectValues()
    {
        $this->assertEquals(10, AnnotationsParser::getPropertyValue($this->testClass, 'age'));
    }
}
