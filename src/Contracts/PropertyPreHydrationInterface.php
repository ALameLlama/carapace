<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Contracts;

use Alamellama\Carapace\Support\Data;
use ReflectionProperty;

/**
 * Interface for attributes that handle modifications to hydration data before it is applied to the object.
 *
 * This enables custom handling of properties before the hydration process.
 * e.g. CastWith, MapFrom
 */
interface PropertyPreHydrationInterface
{
    /**
     * Allows attributes to modify hydration data as needed.
     */
    public function propertyPreHydrate(ReflectionProperty $property, Data $data): void;
}
