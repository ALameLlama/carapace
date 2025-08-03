<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Traits;

use Alamellama\Carapace\Attributes;
use ReflectionClass;
use ReflectionProperty;

/**
 * Trait for serializing objects to arrays and JSON.
 *
 * This trait provides methods to convert an object to an array and to a JSON string.
 * It also supports custom property transformations using attributes.
 */
trait SerializationTrait
{
    /**
     * Converts the object to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [];

        $reflection = new ReflectionClass($this);

        // Only public properties are considered for serialization
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();
            $value = $property->getValue($this);

            foreach ($property->getAttributes() as $attr) {
                $attrInstance = $attr->newInstance();

                // Ran all HandlesPropertyTransform attributes
                // Such as MapTo, etc.
                if ($attrInstance instanceof Attributes\HandlesPropertyTransformation) {
                    ['key' => $name, 'value' => $value] = $attrInstance->handlePropertyTransformation($name, $value);
                    break;
                }
            }

            $result[$name] = $this->recursiveToArray($value);
        }

        return $result;
    }

    /**
     * Converts the object to a JSON string.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * Recursively converts properties to an array.
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
