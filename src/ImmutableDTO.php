<?php

declare(strict_types=1);

namespace Alamellama\Carapace;

use Alamellama\Carapace\Traits\SerializationTrait;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Immutable Data Transfer Object (DTO) Base Class.
 *
 * This abstract class provides a foundation for creating an immutable DTO. It supports:
 * - Hydrating instances from arrays or JSON.
 * - Generating modified copies of the instance with overridden values.
 */
abstract class ImmutableDTO
{
    use SerializationTrait;

    /**
     * Creates a new instance of the DTO from the provided data.
     *
     * @param  string|array<mixed, mixed>  $data  The input data, either as JSON or an associative array.
     * @return static A fully hydrated DTO instance.
     */
    public static function from(string|array $data): static
    {
        // If the data is a JSON string, decode it to an array
        if (is_string($data)) {
            $data = (array) json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        }

        $reflection = new ReflectionClass(static::class);

        // Run all Contracts\PreHydrationHandler attributes
        // Such as CastWith, MapFrom, etc.
        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attr) {
                $attrInstance = $attr->newInstance();
                if ($attrInstance instanceof Contracts\PreHydrationInterface) {
                    $attrInstance->handle($property->getName(), $data);
                }
            }
        }

        $params = $reflection->getConstructor()?->getParameters() ?? [];

        $args = array_map(function (ReflectionParameter $param) use ($data, $reflection) {
            $name = $param->getName();

            // If the parameter is not present in the data, check for default value or nullability
            // and throw an exception if it's required but missing.
            if (! array_key_exists($name, $data)) {
                if ($param->isDefaultValueAvailable()) {
                    return $param->getDefaultValue();
                }

                if ($param->allowsNull()) {
                    return null;
                }

                throw new InvalidArgumentException("Missing required parameter: $name");
            }

            // Run all Contracts\HydrationHandler attributes
            // TODO: currently I don't have a use case for this, but it is here for future use.
            // might be usable for validators or other custom handlers.
            foreach ($reflection->getProperties() as $property) {
                foreach ($property->getAttributes() as $attr) {
                    $attrInstance = $attr->newInstance();
                    if ($attrInstance instanceof Contracts\HydrationInterface) {
                        $attrInstance->handle($property->getName(), $data);
                    }
                }
            }

            $value = $data[$name];
            $type = $param->getType();

            // Handle nested DTO hydration
            if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                $typeName = $type->getName();

                if (is_subclass_of($typeName, self::class) && is_array($value)) {
                    return $typeName::from($value);
                }

                return $value;
            }

            return $value;
        }, $params);

        return $reflection->newInstanceArgs($args);
    }

    /**
     * Creates a modified copy of the DTO with overridden values.
     *
     * @param  array<mixed, mixed>  $overrides  Key-value pairs to override properties.
     * @param mixed $namedOverrides Additional named overrides.

     * @return static A new DTO instance with updated values.
     */
    public function with(array $overrides = [], ...$namedOverrides): static
    {
        $combined = array_merge($overrides, $namedOverrides);

        $reflection = new ReflectionClass($this);
        $params = $reflection->getConstructor()?->getParameters() ?? [];

        $data = [];

        foreach ($params as $param) {
            $name = $param->getName();
            $data[$name] = $combined[$name] ?? $this->{$name} ?? null;
        }

        return static::from($data);
    }
}
