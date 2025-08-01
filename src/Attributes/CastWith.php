<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class CastWith implements HandlesBeforeHydration
{
    public function __construct(
        public string $casterClass
    ) {}

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
