<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Alamellama\Carapace\Contracts\TransformationInterface;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Prevents a property from being included in serialized output.
 *
 * When applied to a property, this attribute will exclude the property
 * when converting the DTO to an array or JSON.
 */
final class Hidden implements TransformationInterface
{
    /**
     * Handles the exclusion of a property from serialized output.
     *
     * @param  string  $propertyName  The name of the property being handled.
     * @param  mixed  $value  The value of the property.
     * @return array{string, mixed} A special signal to indicate this property should be excluded.
     */
    public function handle(string $propertyName, mixed $value): array
    {
        // Return a special signal to indicate this property should be excluded
        return ['__hidden__', null];
    }
}
