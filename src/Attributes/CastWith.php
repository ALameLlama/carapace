<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Attribute;

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

        if (is_array($value)) {
            $data[$propertyName] = array_map(
                fn ($item) => $item instanceof $this->casterClass ? $item : $this->casterClass::from($item),
                $value
            );

            return;
        }

        if ($value instanceof $this->casterClass) {
            return;
        }

        $data[$propertyName] = $this->casterClass::from($value);
    }
}
