<?php

namespace Colloquy\Support;

use ReflectionClass;
use ReflectionProperty;
use Colloquy\Exceptions\MalformedDocBlockException;

class AnnotationsParser
{
    public static function getClassAnnotation(object $object): array
    {
        return self::parseDocComment((new ReflectionClass($object))->getDocComment());
    }

    public static function getPropertyAnnotation(object $object, string $propertyName): array
    {
        return self::getAnnotationFromReflectionProperty((new ReflectionClass($object))->getProperty($propertyName));
    }

    public static function methodAnnotationTagExists(object $object, string $methodName, string $tag): bool
    {
        return array_key_exists($tag, self::getMethodAnnotation($object, $methodName));
    }

    public static function propertyAnnotationTagExists(object $object, string $propertyName, string $tag): bool
    {
        return array_key_exists($tag, self::getPropertyAnnotation($object, $propertyName));
    }

    public static function propertyAnnotationTagValue(object $object, string $propertyName, string $tag): string
    {
        return self::getPropertyAnnotation($object, $propertyName)[$tag];
    }

    public static function getAnnotationFromReflectionProperty(ReflectionProperty $property): array
    {
        return self::parseDocComment($property->getDocComment());
    }

    public static function getPropertyValue(object $object, string $property)
    {
        $property = (new ReflectionClass($object))->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    protected static function getMethodAnnotation(object $object, string $methodName): array
    {
        return self::parseDocComment((new ReflectionClass($object))->getMethod($methodName)->getDocComment());
    }

    protected static function parseDocComment(string $docComment): array
    {
        $raw = str_replace("\r\n", "\n", $docComment);
        $lines = explode("\n", $raw);
        $tags = [];

        $linesCount = count($lines);

        if ($linesCount === 2) {
            throw new MalformedDocBlockException;
        }

        if ($linesCount === 1) {
            if (!preg_match('/\\/\\*\\*([^*]*)\\*\\//', $lines[0], $matches)) {
                return [];
            }

            $lines[0] = substr($lines[0], 3, -2);
        } else {
            array_shift($lines);
            array_pop($lines);
        }

        foreach ($lines as $line) {
            $line = trim(preg_replace('/^[ \t\*]*/', '', $line));

            if (strlen($line) < 2) {
                continue;
            }

            if (preg_match('/^@([^\(\n]+)\((\'([^\']+)\'|"([^"]+)"|([^"]+))\)$/', $line, $matches)) {
                $tagName = $matches[1];
                $tagValue = trim($matches[3]);

                $tags[$tagName] = $tagValue;
            }

            if (preg_match('/@([^ ]+)(.*)/', $line, $matches)) {
                $tagName = $matches[1];
                $tagValue = trim($matches[2]);

                $tags[$tagName] = $tagValue;
            }
        }

        return array_filter($tags, function ($key) {
            return strpos($key, '(') === false;
        }, ARRAY_FILTER_USE_KEY);
    }
}
