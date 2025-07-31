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

            // Get the matching property (for attributes like #[CastWith])
            $property = $reflection->hasProperty($name)
                ? $reflection->getProperty($name)
                : null;

            // TODO: I feel like this could be more elegant, but it works for now
            // we can probably switch to using attributes directly in the future
            if ($property) {
                $castAttr = $property->getAttributes(Attributes\CastWith::class)[0] ?? null;

                if ($castAttr) {
                    /** @var Attributes\CastWith $caster */
                    $caster = $castAttr->newInstance();
                    $targetClass = $caster->casterClass;

                    if (is_array($value)) {
                        return array_map(
                            fn ($item) => $item instanceof $targetClass ? $item : $targetClass::from($item),
                            $value
                        );
                    }

                    return $value instanceof $targetClass ? $value : $targetClass::from($value);
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
