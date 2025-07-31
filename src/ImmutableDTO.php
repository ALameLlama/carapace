<?php

declare(strict_types=1);

namespace Alamellama\Carapace;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Persistent Immutable Data Transfer Object (DTO) base class.
 *
 * Provides methods to create instances from arrays, override properties,
 * and convert to arrays or JSON.
 */
abstract class ImmutableDTO
{
    /**
     * @param  array<mixed, mixed>  $data
     */
    public static function from(array $data): static
    {
        $reflection = new ReflectionClass(static::class);

        // Run all HandlesBeforeHydration attributes
        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attr) {
                $attrInstance = $attr->newInstance();
                if ($attrInstance instanceof Attributes\HandlesBeforeHydration) {
                    $attrInstance->handleBefore($property->getName(), $data);
                }
            }
        }

        $params = $reflection->getConstructor()?->getParameters() ?? [];

        $args = array_map(function (ReflectionParameter $param) use ($data, $reflection) {
            $name = $param->getName();

            if (! array_key_exists($name, $data)) {
                if ($param->isDefaultValueAvailable()) {
                    return $param->getDefaultValue();
                }

                if ($param->allowsNull()) {
                    return null;
                }

                throw new InvalidArgumentException("Missing required parameter: $name");
            }

            $value = $data[$name];
            $type = $param->getType();

            // Handle HandlesPropertyValue attributes (e.g., CastWith, etc.)
            $property = $reflection->hasProperty($name)
                ? $reflection->getProperty($name)
                : null;

            if ($property) {
                foreach ($property->getAttributes() as $attr) {
                    $attrInstance = $attr->newInstance();

                    if ($attrInstance instanceof Attributes\HandlesPropertyValue) {
                        $value = $attrInstance->handle($value, $data);
                    }
                }
            }

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
     * @param  array<mixed,mixed>  $overrides
     * @param  mixed  $namedOverrides
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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $props = get_object_vars($this);

        return array_map(
            fn ($value): mixed => $this->recursiveToArray($value),
            $props
        );
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    private function recursiveToArray(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item): mixed => $this->recursiveToArray($item), $value);
        }

        if ($value instanceof self) {
            return $value->toArray();
        }

        return $value;
    }
}
