<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\PropertyTransformationInterface;
use Attribute;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Prevents a property from being included in serialized output.
 *
 * When applied to a property, this attribute will exclude the property
 * when converting the DTO to an array or JSON.
 */
final class Hidden implements PropertyTransformationInterface
{
    /**
     * Handles the exclusion of a property from serialized output.
     *
     * @return array{string, mixed} A special signal to indicate this property should be excluded.
     */
    public function propertyTransform(ReflectionProperty $property, mixed $value): array
    {
        // Return a special signal to indicate this property should be excluded
        return ['__hidden__', null];
    }
}
