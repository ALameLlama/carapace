<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\ClassPreHydrationInterface;
use Alamellama\Carapace\Contracts\PropertyPreHydrationInterface;
use Alamellama\Carapace\Support\Data;
use Attribute;
use ReflectionProperty;

use function is_null;

/**
 * ConvertEmptyToNull attribute
 *
 * Pre-hydration handler that converts empty input values (empty string "" or empty array []) to null,
 * but only if the target property type allows null.
 *
 * Can be applied at class level (affects all properties) or property level (affects only that property).
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class ConvertEmptyToNull implements ClassPreHydrationInterface, PropertyPreHydrationInterface
{
    public function classPreHydrate(ReflectionProperty $property, Data $data): void
    {
        $this->convert($property, $data);
    }

    public function propertyPreHydrate(ReflectionProperty $property, Data $data): void
    {
        $this->convert($property, $data);
    }

    private function convert(ReflectionProperty $property, Data $data): void
    {
        $type = $property->getType();

        if (is_null($type) || ! $type->allowsNull()) {
            return;
        }

        $name = $property->getName();

        if (! $data->has($name)) {
            return;
        }

        $value = $data->get($name);

        if (is_null($value)) {
            return;
        }

        if (! empty($value)) {
            return;
        }

        $data->set($name, null);
    }
}
