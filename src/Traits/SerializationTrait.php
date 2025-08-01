<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Traits;

use Alamellama\Carapace\Attributes;
use ReflectionClass;

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

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            $value = $property->getValue($this);

            // Look for HandlesPropertyTransform attributes
            foreach ($property->getAttributes() as $attr) {
                $attrInstance = $attr->newInstance();

                if ($attrInstance instanceof Attributes\HandlesPropertyTransform) {
                    ['key' => $name, 'value' => $value] = $attrInstance->handle($name, $value);
                    break; // Assume only one mapping attribute
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
