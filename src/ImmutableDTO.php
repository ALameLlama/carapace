<?php

declare(strict_types=1);

namespace Alamellama\Carapace;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

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
