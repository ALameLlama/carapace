<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Contracts;

use ReflectionProperty;

/**
 * Interface for attributes that handle property transformations when converting to an array.
 *
 * This enables custom handling of properties during the transformation process.
 * e.g. MapTo
 */
interface PropertyTransformationInterface
{
    /**
     * Allows attributes to modify property when transforming to array.
     *
     * @param  ReflectionProperty  $property  The property being handled.
     * @param  mixed  $value  The value of the property.
     * @return array{string, mixed} The key-value pair to be used in the array.
     */
    public function propertyTransform(ReflectionProperty $property, mixed $value): array;
}
