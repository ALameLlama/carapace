<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Traits;

use const JSON_THROW_ON_ERROR;

use Alamellama\Carapace\Attributes\Hidden;
use Alamellama\Carapace\Contracts;
use JsonException;
use ReflectionClass;
use ReflectionProperty;

use function is_array;

/**
 * Trait for Serializing Objects.
 *
 * Provides utility methods to:
 * - Convert an object to an array.
 * - Serialize an object to a JSON string.
 * - Apply attribute-based property transformations.
 */
trait SerializationTrait
{
    /**
     * Converts the object into an associative array.
     *
     * @return array<string, mixed> An array representation of the object's public properties.
     */
    public function toArray(): array
    {
        $result = [];

        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        if (empty($properties)) {
            return $result;
        }

        foreach ($properties as $property) {
            $name = $property->getName();
            $value = $property->getValue($this);

            foreach ($property->getAttributes() as $attr) {
                $attrInstance = $attr->newInstance();
                // Run all PropertyTransformationInterface attributes
                // Such as MapTo, Hidden, etc.
                if ($attrInstance instanceof Contracts\PropertyTransformationInterface) {
                    [$name, $value] = $attrInstance->propertyTransform($property, $value);
                }

                if ($name === Hidden::SIGNAL) {
                    continue 2;
                }
            }

            foreach ($reflection->getAttributes() as $classAttr) {
                $classAttrInstance = $classAttr->newInstance();
                if ($classAttrInstance instanceof Contracts\ClassTransformationInterface) {
                    $originalName = $name;
                    // Run all ClassTransformationInterface attributes
                    // Such as SnakeCase, etc.
                    [$proposedName, $value] = $classAttrInstance->classTransform($property, $value);
                    if ($proposedName === Hidden::SIGNAL) {
                        $name = $proposedName;

                        continue 2;
                    }

                    // TODO: I need to see if I am happy with this, might need to scope this better instead of it being global.
                    // If the class-level transform returns the original property name,
                    // preserve the name produced by property-level transforms (if any).
                    $name = $proposedName === $property->getName() ? $originalName : $proposedName;
                }
            }

            $result[$name] = $this->recursiveToArray($value);
        }

        return $result;
    }

    /**
     * Serializes the object into a JSON string.
     *
     * @return string A JSON-encoded representation of the object.
     *
     * @throws JsonException If encoding fails.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * Recursively transforms nested properties into arrays.
     *
     * @param  mixed  $value  The value to be converted.
     * @return mixed The transformed array or original value if not transformable.
     */
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
