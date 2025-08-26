<?php

declare(strict_types=1);

namespace Alamellama\Carapace;

use Alamellama\Carapace\Support\Data as DataWrapper;
use Alamellama\Carapace\Traits\GetParentAttributesTrait;
use Alamellama\Carapace\Traits\SerializationTrait;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Immutable Data Transfer Object (DTO) Base Class.
 */
abstract class Data
{
    use GetParentAttributesTrait;
    use SerializationTrait;

    /**
     * Creates a new instance of the DTO from the provided data.
     *
     * @param  string|array<mixed, mixed>|object  $data  The input data, either as JSON, associative array, or model-like object.
     * @return static A fully hydrated DTO instance.
     */
    public static function from(string|array|object $data): static
    {
        $data = DataWrapper::wrap($data);
        $reflection = new ReflectionClass(static::class);

        // Run all Contracts\ClassPreHydrationInterface attributes
        foreach (self::getParentAttributes($reflection) as $classAttr) {
            $classAttrInstance = $classAttr->newInstance();
            if ($classAttrInstance instanceof Contracts\ClassPreHydrationInterface) {
                foreach ($reflection->getProperties() as $property) {
                    $classAttrInstance->classPreHydrate($property, $data);
                }
            }
        }

        // Run all Contracts\PreHydrationHandler attributes
        // Such as CastWith, MapFrom, etc.
        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attr) {
                $attrInstance = $attr->newInstance();
                if ($attrInstance instanceof Contracts\PropertyPreHydrationInterface) {
                    $attrInstance->propertyPreHydrate($property, $data);
                }
            }
        }

        $params = $reflection->getConstructor()?->getParameters() ?? [];

        $args = array_map(static function (ReflectionParameter $param) use ($reflection, $data) {
            $name = $param->getName();

            if (! $data->has($name)) {
                if ($param->isDefaultValueAvailable()) {
                    return $param->getDefaultValue();
                }

                if ($param->allowsNull()) {
                    return null;
                }

                throw new InvalidArgumentException("Missing required parameter: $name");
            }

            // Run all Contracts\ClassHydrationInterface attributes
            foreach ($reflection->getAttributes() as $classAttr) {
                $classAttrInstance = $classAttr->newInstance();
                if ($classAttrInstance instanceof Contracts\ClassHydrationInterface) {
                    foreach ($reflection->getProperties() as $property) {
                        $classAttrInstance->classHydrate($property, $data);
                    }
                }
            }

            // Run all Contracts\HydrationHandler attributes
            // This can be used for validators or other custom handlers.
            foreach ($reflection->getProperties() as $property) {
                foreach ($property->getAttributes() as $attr) {
                    $attrInstance = $attr->newInstance();
                    if ($attrInstance instanceof Contracts\PropertyHydrationInterface) {
                        $attrInstance->propertyHydrate($property, $data);
                    }
                }
            }

            $value = $data->get($name);

            $type = $param->getType();

            if (! ($type instanceof ReflectionNamedType) || $type->isBuiltin()) {
                return $value;
            }

            $typeName = $type->getName();

            if (is_subclass_of($typeName, self::class) && DataWrapper::isArrayOrObject($value)) {
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
        $items = DataWrapper::wrap($data)->items();

        /** @var array<int, array<mixed, mixed>|object> $items */
        return array_map(static fn (array|object $dto): static => static::from($dto), $items);
    }

    /**
     * Creates a modified copy of the DTO with overridden values.

     *
     * @param  array<mixed, mixed>  $overrides  Key-value pairs to override properties.
     * @param  mixed  $namedOverrides  Additional named overrides.

     * @return static A new DTO instance with updated values.
     */
    public function with(array|object $overrides = [], ...$namedOverrides): static
    {
        $baseOverrides = DataWrapper::wrap($overrides)->toArray();
        $combined = array_merge($baseOverrides, $namedOverrides);

        $reflection = new ReflectionClass($this);
        $params = $reflection->getConstructor()?->getParameters() ?? [];

        if (empty($params)) {
            return static::from([]);
        }

        $data = [];

        foreach ($params as $param) {
            $name = $param->getName();
            $data[$name] = $combined[$name] ?? $this->{$name} ?? null;
        }

        return static::from($data);
    }
}
