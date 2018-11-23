<?php

namespace Colloquy;

use ReflectionClass;
use ReflectionException;
use Colloquy\Support\AnnotationsParser;

class ColloquyAnnotations
{
    public const ColloquyPersist = 'ColloquyPersist';
    public const Begin = 'ColloquyBegin';
    public const End = 'ColloquyEnd';

    protected $annotationsParser;

    public function __construct()
    {
        $this->annotationsParser = new AnnotationsParser;
    }

    public static function handle(object $object, $method)
    {
        if (AnnotationsParser::methodAnnotationTagExists($object, $method, ColloquyAnnotations::Begin)) {
            self::createContextFromObject($object);
        } elseif (AnnotationsParser::methodAnnotationTagExists($object, $method, ColloquyAnnotations::End)) {
            self::contextFromObject($object)->end();
        } else {
            self::injectPersistedState($object);
        }
    }

    public static function persist(object $object)
    {
        $annotation = AnnotationsParser::getClassAnnotation($object);
        $contextName = $annotation['ColloquyContext'];

        if (!Colloquy::makeSelfFromBinding($contextName)->contextExists($contextName, $object)) {
            return;
        }

        $context = self::contextFromObject($object);

        foreach (self::getProperties($object) as $propertyName => $propertyAnnotationTags) {
            if (array_key_exists(ColloquyAnnotations::ColloquyPersist, $propertyAnnotationTags)) {
                $identifier = self::getIdentifierForProperty($propertyName, $propertyAnnotationTags, $object);

                $context->set(AnnotationsParser::getPropertyValue($object, $propertyName), $identifier);
            }
        }
    }

    protected static function getIdentifierForProperty($propertyName, $propertyAnnotationTags, $object)
    {
        $identifier = $propertyAnnotationTags[ColloquyAnnotations::ColloquyPersist];

        if ($identifier) {
            return $identifier;
        }

        return vsprintf('%s.%s.%s', [
            Colloquy::PREFIX,
            get_class($object),
            $propertyName,
        ]);
    }

    private static function getProperties(object $object): array
    {
        $result = [];

        try {
            $properties = (new ReflectionClass($object))->getProperties();
        } catch (ReflectionException $e) {
            return [];
        }

        foreach ($properties as $property) {
            $result[$property->getName()] = AnnotationsParser::getAnnotationFromReflectionProperty($property);
        }

        return $result;
    }

    public static function contextNameFromObject(object $object): string
    {
        $annotation = AnnotationsParser::getClassAnnotation($object);

        return $annotation['ColloquyContext'];
    }

    private static function contextFromObject(object $object): ColloquyContext
    {
        return Colloquy::getBoundContext(self::contextNameFromObject($object), $object);
    }

    private static function createContextFromObject(object $object): void
    {
        $contextName = self::contextNameFromObject($object);

        Colloquy::createContextFromBinding($contextName, $object);
    }

    private static function injectPersistedState(object $object): void
    {
        try {
            $context = self::contextFromObject($object);
            $properties = (new ReflectionClass($object))->getProperties();

            foreach ($properties as $property) {
                if (AnnotationsParser::propertyAnnotationTagExists(
                    $object,
                    $property->getName(),
                    ColloquyAnnotations::ColloquyPersist
                )) {
                    $identifier = self::getIdentifierForProperty(
                        $property->getName(),
                        AnnotationsParser::getAnnotationFromReflectionProperty($property),
                        $object
                    );

                    $property->setAccessible(true);
                    $property->setValue($object, $context->get($identifier));
                }
            }
        } catch (ReflectionException $e) {
            //
        }
    }
}
