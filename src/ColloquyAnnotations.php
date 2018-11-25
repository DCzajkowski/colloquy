<?php

namespace Colloquy;

use ReflectionClass;
use ReflectionException;
use Colloquy\Support\AnnotationsParser;

class ColloquyAnnotations
{
    protected const ANNOTATION_PERSIST = 'ColloquyPersist';
    protected const ANNOTATION_BEGIN = 'ColloquyBegin';
    protected const ANNOTATION_END = 'ColloquyEnd';

    public static function handle(object $object, $method)
    {
        if (AnnotationsParser::methodAnnotationTagExists($object, $method, self::ANNOTATION_BEGIN)) {
            self::createContextFromObject($object);

            return;
        }

        if (AnnotationsParser::methodAnnotationTagExists($object, $method, self::ANNOTATION_END)) {
            Colloquy::addContextToBeRemoved(self::contextFromObject($object));
        }

        self::injectPersistedState($object);
    }

    public static function endTransaction(object $object)
    {
        $contextName = self::contextNameFromObject($object);

        if (!$contextName) {
            throw new NoDefinedContextException($object);
        }

        if (!Colloquy::makeSelfFromBinding($contextName)->contextExists($contextName, $object)) {
            return;
        }

        $context = self::contextFromObject($object);

        if (Colloquy::shouldBeRemoved($context)) {
            Colloquy::removeContext($context);

            return;
        }

        foreach (self::getProperties($object) as $propertyName => $propertyAnnotationTags) {
            if (AnnotationsParser::propertyAnnotationTagExists(
                $object,
                $propertyName,
                self::ANNOTATION_PERSIST
            )) {
                $identifier = self::getIdentifierForProperty($propertyName, $propertyAnnotationTags, $object);

                $context->set(AnnotationsParser::getPropertyValue($object, $propertyName), $identifier);
            }
        }
    }

    protected static function getIdentifierForProperty(string $propertyName, array $propertyAnnotationTags, object $object): string
    {
        $identifier = $propertyAnnotationTags[self::ANNOTATION_PERSIST];

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
        $context = self::contextFromObject($object);
        $properties = (new ReflectionClass($object))->getProperties();

        foreach ($properties as $property) {
            if (AnnotationsParser::propertyAnnotationTagExists(
                $object,
                $property->getName(),
                self::ANNOTATION_PERSIST
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
    }
}
