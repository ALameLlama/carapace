<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\CasterInterface;
use Alamellama\Carapace\Contracts\PreHydrationInterface;
use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Casts a property using either a DTO class or a CasterInterface implementation.
 *
 * This ensures the property is properly cast to the desired type during hydration.
 */
final class CastWith implements PreHydrationInterface
{
    /**
     * @param  string|CasterInterface  $caster  Either a DTO class name or a CasterInterface instance
     */
    public function __construct(
        public string|CasterInterface $caster
    ) {}

    /**
     * Handles the casting of a property using either a DTO class or a CasterInterface implementation.
     *
     * @param  string  $propertyName  The name of the property being handled.
     * @param  array<mixed>  $data  The data being hydrated.
     *
     * @throws InvalidArgumentException If the value cannot be cast properly.
     */
    public function handle(string $propertyName, array &$data): void
    {
        if (! array_key_exists($propertyName, $data)) {
            return;
        }

        $value = $data[$propertyName];

        // If using a CasterInterface implementation
        if ($this->caster instanceof CasterInterface) {
            $data[$propertyName] = $this->caster->cast($value);

            return;
        }

        // If the value is already an instance of the target class, no need to cast
        if ($value instanceof $this->caster) {
            return;
        }

        // Collection of DTOs
        if (is_array($value) && array_is_list($value)) {
            $data[$propertyName] = array_map(
                fn ($item) => $item instanceof $this->caster ? $item : $this->caster::from($item),
                $value
            );

            return;
        }

        // Single DTO array
        if (is_array($value)) {
            $data[$propertyName] = $this->caster::from($value);

            return;
        }

        throw new InvalidArgumentException("Unable to cast property '{$propertyName}' to " . $this->caster);
    }
}
