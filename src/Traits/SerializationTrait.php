<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Traits;

trait SerializationTrait
{
    /**
     * Converts the object to an array.
     *
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
