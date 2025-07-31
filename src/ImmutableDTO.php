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
 *
 * @template T of ImmutableDTO
 */
abstract class ImmutableDTO
{
    /**
     * @param  array<mixed, mixed>  $data
     */
    public static function from(array $data): static
    {
        $reflection = new ReflectionClass(static::class);
        $params = $reflection->getConstructor()?->getParameters() ?? [];

        $args = array_map(function (ReflectionParameter $param) use ($data) {
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
        return array_map(function ($value) {
            // Recursively convert nested DTOs to arrays
            if ($value instanceof self) {
                return $value->toArray();
            }

            return $value;
        }, get_object_vars($this));
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}
