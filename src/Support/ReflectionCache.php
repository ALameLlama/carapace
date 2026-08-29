<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Support;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;

use function is_object;

final class ReflectionCache
{
    /** @var array<class-string, ReflectionClass<object>> */
    private static array $reflections = [];

    /** @var array<class-string, list<ReflectionProperty>> */
    private static array $properties = [];

    /** @var array<class-string, list<ReflectionProperty>> */
    private static array $publicProperties = [];

    /** @var array<class-string, list<ReflectionParameter>> */
    private static array $parameters = [];

    /** @var array<class-string, array<string, ReflectionProperty>> */
    private static array $parameterProperties = [];

    /** @var array<class-string, array<class-string, list<ReflectionAttribute<object>>>> */
    private static array $parentAttributes = [];

    /** @var array<class-string, array<string, array<class-string, list<ReflectionAttribute<object>>>>> */
    private static array $propertyAttributes = [];

    /** @var array<class-string, array<string, array<class-string, bool>>> */
    private static array $propertyAttributeNames = [];

    /**
     * @template TObject of object
     *
     * @param  class-string<TObject>|TObject  $class
     * @return ReflectionClass<TObject>
     */
    public static function reflection(object|string $class): ReflectionClass
    {
        $name = is_object($class) ? $class::class : $class;

        /** @var ReflectionClass<TObject> $reflection */
        $reflection = self::$reflections[$name] ??= new ReflectionClass($name);

        return $reflection;
    }

    /** @param class-string $class
     * @return list<ReflectionProperty>
     */
    public static function properties(string $class): array
    {
        return self::$properties[$class] ??= self::reflection($class)->getProperties();
    }

    /** @param class-string $class
     * @return list<ReflectionProperty>
     */
    public static function publicProperties(string $class): array
    {
        return self::$publicProperties[$class] ??= self::reflection($class)->getProperties(ReflectionProperty::IS_PUBLIC);
    }

    /** @param class-string $class
     * @return list<ReflectionParameter>
     */
    public static function constructorParameters(string $class): array
    {
        return self::$parameters[$class] ??= self::reflection($class)->getConstructor()?->getParameters() ?? [];
    }

    /** @param class-string $class */
    public static function parameterProperty(string $class, string $parameter): ?ReflectionProperty
    {
        if (! isset(self::$parameterProperties[$class])) {
            foreach (self::properties($class) as $property) {
                self::$parameterProperties[$class][$property->getName()] = $property;
            }
        }

        return self::$parameterProperties[$class][$parameter] ?? null;
    }

    /**
     * @template TAttribute of object
     *
     * @param  class-string  $class
     * @param  class-string<TAttribute>  $interface
     * @return list<ReflectionAttribute<TAttribute>>
     */
    public static function parentAttributes(string $class, string $interface): array
    {
        if (! isset(self::$parentAttributes[$class][$interface])) {
            $attributes = [];
            $current = self::reflection($class);
            while ($current !== false) {
                array_push($attributes, ...$current->getAttributes($interface, ReflectionAttribute::IS_INSTANCEOF));
                $current = $current->getParentClass();
            }

            self::$parentAttributes[$class][$interface] = $attributes;
        }

        /** @var list<ReflectionAttribute<TAttribute>> $attributes */
        $attributes = self::$parentAttributes[$class][$interface];

        return $attributes;
    }

    /**
     * @template TAttribute of object
     *
     * @param  class-string<TAttribute>  $interface
     * @return list<ReflectionAttribute<TAttribute>>
     */
    public static function propertyAttributes(ReflectionProperty $property, string $interface): array
    {
        $class = $property->getDeclaringClass()->getName();
        $name = $property->getName();

        /** @var list<ReflectionAttribute<TAttribute>> $attributes */
        $attributes = self::$propertyAttributes[$class][$name][$interface] ??= $property->getAttributes($interface, ReflectionAttribute::IS_INSTANCEOF);

        return $attributes;
    }

    /** @param class-string $attribute */
    public static function propertyHasAttribute(ReflectionProperty $property, string $attribute): bool
    {
        $class = $property->getDeclaringClass()->getName();
        $name = $property->getName();

        return self::$propertyAttributeNames[$class][$name][$attribute] ??= $property->getAttributes($attribute) !== [];
    }
}
