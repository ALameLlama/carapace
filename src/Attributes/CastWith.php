<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Interfaces\PreHydrationHandler;
use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Casts a property to a specific DTO class when hydrating an object.
 * This is useful for ensuring that the property is always represented as a specific DTO type.
 */
final class CastWith implements PreHydrationHandler
{
    public function __construct(
        public string $casterClass
    ) {}

    /**
     * Handles the casting of a property to a specific DTO class.
     *
     * @param  string  $propertyName  The name of the property being handled.
     * @param  array<mixed>  $data  The data being hydrated.
     *
     * @throws InvalidArgumentException If the value cannot be cast to the specified DTO class.
     */
    public function handle(string $propertyName, array &$data): void
    {
        if (! array_key_exists($propertyName, $data)) {
            return;
        }

        $value = $data[$propertyName];

        if ($value instanceof $this->casterClass) {
            return;
        }

        // Collection of DTOs
        if (is_array($value) && array_is_list($value)) {
            $data[$propertyName] = array_map(
                fn ($item) => $item instanceof $this->casterClass ? $item : $this->casterClass::from($item),
                $value
            );

            return;
        }

        // Single DTO array
        if (is_array($value)) {
            $data[$propertyName] = $this->casterClass::from($value);

            return;
        }

        throw new InvalidArgumentException("Unable to cast property '{$propertyName}' to {$this->casterClass}");
    }
}
