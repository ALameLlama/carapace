<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Traits;

use Alamellama\Carapace\Contracts\ClassHydrationInterface;
use Alamellama\Carapace\Contracts\ClassPreHydrationInterface;
use Alamellama\Carapace\Contracts\DTOInterface;
use Alamellama\Carapace\Contracts\PropertyHydrationInterface;
use Alamellama\Carapace\Contracts\PropertyPreHydrationInterface;
use Alamellama\Carapace\Support\Data;
use Alamellama\Carapace\Support\ReflectionCache;
use InvalidArgumentException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

use function array_key_exists;
use function is_array;
use function is_object;

trait DTOTrait
{
    use SerializationTrait;

    /**
     * Creates a new instance of the DTO from the provided data.
     *
     * @param  string|array<mixed, mixed>|object  $data  The input data, either as JSON, associative array, or model-like object.
     * @return static A fully hydrated DTO instance.
     */
    public static function from(string|array|object $data): static
    {
        $data = Data::wrap($data);
        $class = static::class;
        $reflection = ReflectionCache::reflection($class);
        $properties = ReflectionCache::properties($class);

        // Run all Contracts\ClassPreHydrationInterface attributes
        foreach (ReflectionCache::parentAttributes($class, ClassPreHydrationInterface::class) as $classAttr) {
            $classAttrInstance = $classAttr->newInstance();
            foreach ($properties as $property) {
                $classAttrInstance->classPreHydrate($property, $data);
            }
        }

        // Run all Contracts\PropertyPreHydrationInterface attributes
        // Such as CastWith, MapFrom, etc.
        foreach ($properties as $property) {
            foreach (ReflectionCache::propertyAttributes($property, PropertyPreHydrationInterface::class) as $attr) {
                $attrInstance = $attr->newInstance();
                $attrInstance->propertyPreHydrate($property, $data);
            }
        }

        $params = ReflectionCache::constructorParameters($class);
        $classHydrationAttributes = ReflectionCache::parentAttributes($class, ClassHydrationInterface::class);

        $args = array_map(static function (ReflectionParameter $param) use ($class, $data, $classHydrationAttributes) {
            $name = $param->getName();

            if (! $data->has($name)) {
                if ($param->isDefaultValueAvailable()) {
                    return $param->getDefaultValue();
                }

                if ($param->allowsNull()) {
                    return null;
                }

                throw new InvalidArgumentException("Missing required parameter: {$name}");
            }

            $property = ReflectionCache::parameterProperty($class, $name);

            if (! $property instanceof ReflectionProperty) {
                goto skipPropertyHydration;
            }

            // Run all Contracts\ClassHydrationInterface attributes
            foreach ($classHydrationAttributes as $classAttr) {
                $classAttrInstance = $classAttr->newInstance();
                $classAttrInstance->classHydrate($property, $data);
            }

            // Run all Contracts\PropertyHydrationInterface attributes
            // This can be used for validators or other custom handlers.
            foreach (ReflectionCache::propertyAttributes($property, PropertyHydrationInterface::class) as $attr) {
                $attrInstance = $attr->newInstance();
                $attrInstance->propertyHydrate($property, $data);
            }

            skipPropertyHydration:

            $value = $data->get($name);

            $type = $param->getType();

            if (! ($type instanceof ReflectionNamedType) || $type->isBuiltin()) {
                return $value;
            }

            $typeName = $type->getName();

            if ((is_array($value) || is_object($value)) && is_a($typeName, DTOInterface::class, true)) {
                /** @var array<mixed, mixed>|object $value */
                return $typeName::from($value);
            }

            return $value;
        }, $params);

        return $reflection->newInstanceArgs($args);
    }

    /**
     * Creates an array of DTOs from the provided data.
     *
     * @param  string|array<array<mixed, mixed>|object>|object  $data  The input data, either as JSON, array, or object containing items.
     * @return static[] A fully hydrated array of DTO instances.
     */
    public static function collect(string|array|object $data): array
    {
        $items = Data::wrap($data)->items();

        /** @var array<int, array<mixed, mixed>|object> $items */
        return array_map(static::from(...), $items);
    }

    /**
     * Creates a modified copy of the DTO with overridden values.
     *
     * When a property is a DTO and the override value is an array, the array is
     * recursively merged into the existing DTO. Otherwise the value
     * replaces the property entirely.
     *
     * @param  array<mixed, mixed>  $overrides  Key-value pairs to override properties.
     * @param  mixed  ...$namedOverrides  Additional named overrides as variadic arguments.
     *                                    Each argument should be an array of key-value pairs to override properties.
     * @return static A new DTO instance with updated values.
     */
    public function with(array|object $overrides = [], ...$namedOverrides): static
    {
        $baseOverrides = Data::wrap($overrides)->toArray();
        $combined = array_merge($baseOverrides, $namedOverrides);

        $params = ReflectionCache::constructorParameters(static::class);

        if ($params === []) {
            return static::from([]);
        }

        $data = [];

        foreach ($params as $param) {
            $name = $param->getName();

            if (array_key_exists($name, $combined)) {
                $type = $param->getType();
                $value = $combined[$name];
                $existingValue = $this->{$name} ?? null;

                if (! ($type instanceof ReflectionNamedType) || $type->isBuiltin()) {
                    $data[$name] = $value;

                    continue;
                }

                // If the property is a DTO type and the override is an array,
                // recursively merge the diff into the existing DTO.
                if (
                    is_array($value) &&
                    is_object($existingValue) &&
                    is_a($type->getName(), DTOInterface::class, true) &&
                    is_a($existingValue::class, DTOInterface::class, true)
                ) {
                    /** @var DTOInterface $existingValue */
                    $value = $existingValue->with($value);
                }

                $data[$name] = $value;

                continue;
            }

            $data[$name] = $this->{$name} ?? null;
        }

        return static::from($data);
    }
}
