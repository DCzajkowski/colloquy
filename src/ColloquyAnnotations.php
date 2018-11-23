<?php

namespace Colloquy;

use ReflectionClass;
use ReflectionException;
use Colloquy\Support\AnnotationsParser;

class ColloquyAnnotations
{
    protected const AnnotationPersist = 'ColloquyPersist';
    protected const AnnotationBegin = 'ColloquyBegin';
    protected const AnnotationEnd = 'ColloquyEnd';

    public static function handle(object $object, $method)
    {
        if (AnnotationsParser::methodAnnotationTagExists($object, $method, ColloquyAnnotations::AnnotationBegin)) {
            self::createContextFromObject($object);
        } elseif (AnnotationsParser::methodAnnotationTagExists($object, $method, ColloquyAnnotations::AnnotationEnd)) {
            self::contextFromObject($object)->end();
        } else {
            self::injectPersistedState($object);
        }
    }

    public static function endTransaction(object $object)
    {
        $annotation = AnnotationsParser::getClassAnnotation($object);
        $contextName = $annotation['ColloquyContext'];

        if (!Colloquy::makeSelfFromBinding($contextName)->contextExists($contextName, $object)) {
            return;
        }

        $context = self::contextFromObject($object);

        foreach (self::getProperties($object) as $propertyName => $propertyAnnotationTags) {
            if (AnnotationsParser::propertyAnnotationTagExists(
                $object,
                $propertyName,
                ColloquyAnnotations::AnnotationPersist
            )) {
                $identifier = self::getIdentifierForProperty($propertyName, $propertyAnnotationTags, $object);

                $context->set(AnnotationsParser::getPropertyValue($object, $propertyName), $identifier);
            }
        }
    }

    protected static function getIdentifierForProperty(string $propertyName, array $propertyAnnotationTags, object $object): string
    {
        $identifier = $propertyAnnotationTags[ColloquyAnnotations::AnnotationPersist];

        if ($identifier) {
            return $identifier;
        }

        return vsprintf('%s.%s.%s', [
            Colloquy::PREFIX,
            get_class($object),
            $propertyName,
        ]);
    }

    protected static function getProperties(object $object): array
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

    protected static function contextNameFromObject(object $object): string
    {
        $annotation = AnnotationsParser::getClassAnnotation($object);

        return $annotation['ColloquyContext'];
    }

    protected static function contextFromObject(object $object): ColloquyContext
    {
        return Colloquy::getBoundContext(self::contextNameFromObject($object), $object);
    }

    protected static function createContextFromObject(object $object): void
    {
        $contextName = self::contextNameFromObject($object);

        Colloquy::createContextFromBinding($contextName, $object);
    }

    protected static function injectPersistedState(object $object): void
    {
        try {
            $context = self::contextFromObject($object);
            $properties = (new ReflectionClass($object))->getProperties();

            foreach ($properties as $property) {
                if (AnnotationsParser::propertyAnnotationTagExists(
                    $object,
                    $property->getName(),
                    ColloquyAnnotations::AnnotationPersist
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
