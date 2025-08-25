<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\ClassPreHydrationInterface;
use Alamellama\Carapace\Contracts\ClassTransformationInterface;
use Alamellama\Carapace\Contracts\PropertyPreHydrationInterface;
use Alamellama\Carapace\Contracts\PropertyTransformationInterface;
use Alamellama\Carapace\Support\Data;
use Alamellama\Carapace\Traits\PropertyHasAttribute;
use Attribute;
use ReflectionProperty;

/**
 * Applies snake_case mapping for properties.
 *
 * - On hydration: maps from snake_case input keys to camelCase property names (MapFrom equivalent).
 * - On serialization: maps from camelCase property names to snake_case output keys (MapTo equivalent).
 *
 * Can be applied to a class to affect all properties, or to an individual property.
 * Explicit MapTo/MapFrom attributes on a property take precedence over class-level SnakeCase.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class SnakeCase implements ClassPreHydrationInterface, ClassTransformationInterface, PropertyPreHydrationInterface, PropertyTransformationInterface
{
    use PropertyHasAttribute;

    /**
     * Class-level pre-hydration: remap snake_case keys to property names unless the property
     * already defines its own MapFrom attribute or the camelCase key is present.
     */
    public function classPreHydrate(ReflectionProperty $property, Data $data): void
    {
        // If a property already has its own MapFrom attribute, don't interfere
        if ($this->propertyHasAttribute($property, MapFrom::class)) {
            return;
        }

        $camel = $property->getName();
        if ($data->has($camel)) {
            return;
        }

        $snake = $this->toSnake($camel);

        if ($data->has($snake)) {
            $data->set($camel, $data->get($snake));
            $data->unset($snake);
        }
    }

    /**
     * Class-level transform: output snake_case key unless the property has an explicit MapTo.
     */
    public function classTransform(ReflectionProperty $property, mixed $value): array
    {
        // Respect explicit MapTo on the property
        if ($this->propertyHasAttribute($property, MapTo::class)) {
            return [$property->getName(), $value];
        }

        $snake = $this->toSnake($property->getName());

        return [$snake, $value];
    }

    /**
     * Property-level pre-hydration: same as class-level but scoped to this property.
     */
    public function propertyPreHydrate(ReflectionProperty $property, Data $data): void
    {
        $camel = $property->getName();
        if ($data->has($camel)) {
            return;
        }

        $snake = $this->toSnake($camel);
        if ($data->has($snake)) {
            $data->set($camel, $data->get($snake));
            $data->unset($snake);
        }
    }

    /**
     * Property-level transform: map property name to snake_case on serialization.
     */
    public function propertyTransform(ReflectionProperty $property, mixed $value): array
    {
        $snake = $this->toSnake($property->getName());

        return [$snake, $value];
    }

    /**
     * Convert a camelCase or PascalCase string to snake_case.
     */
    private function toSnake(string $name): string
    {
        // Replace camelCase boundaries with underscore and lowercase the result
        $snake = preg_replace('/(?<!^)[A-Z]/', '_$0', $name);

        return strtolower((string) $snake);
    }
}
