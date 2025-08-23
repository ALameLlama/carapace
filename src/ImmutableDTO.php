<?php

declare(strict_types=1);

namespace Alamellama\Carapace;

use Alamellama\Carapace\Support\Data;
use Alamellama\Carapace\Traits\SerializationTrait;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

use function is_array;
use function is_object;

/**
 * Immutable Data Transfer Object (DTO) Base Class.
 */
abstract class ImmutableDTO
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

            // Run all Contracts\HydrationHandler attributes
            // This can be used for validators or other custom handlers.
            foreach ($reflection->getProperties() as $property) {
                foreach ($property->getAttributes() as $attr) {
                    $attrInstance = $attr->newInstance();
                    if ($attrInstance instanceof Contracts\HydrationInterface) {
                        $attrInstance->handle($property->getName(), $data);
                    }
                }
            }

            $value = $data->get($name);

            $type = $param->getType();

            if (! ($type instanceof ReflectionNamedType) || $type->isBuiltin()) {
                return $value;
            }

            $typeName = $type->getName();

            if (is_subclass_of($typeName, self::class) && (is_array($value) || is_object($value))) {
                return $typeName::from($value);
            }

            return $value;
        }, $params);

        return $reflection->newInstanceArgs($args);
    }

    /**
     * Creates an array of DTOs from the provided data.
     *
     * @param  string|array<array<mixed>>  $data  The input data, either as JSON or array.
     * @return static[] A fully hydrated array of DTO instances.
     */
    public static function collect(string|array $data): array
    {
        $data = Data::wrap($data);
        /** @var array<int, array<mixed, mixed>> $normalized */
        $normalized = $data->raw();

        return array_map(static fn (array $dto): static => static::from($dto), $normalized);
    }

    /**
     * Creates a modified copy of the DTO with overridden values.

     *
     * @param  array<mixed, mixed>  $overrides  Key-value pairs to override properties.
     * @param  mixed  $namedOverrides  Additional named overrides.

     * @return static A new DTO instance with updated values.
     */
    public function with(array $overrides = [], ...$namedOverrides): static
    {
        $combined = array_merge($overrides, $namedOverrides);

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
