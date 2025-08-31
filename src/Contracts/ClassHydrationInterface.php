<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Contracts;

use Alamellama\Carapace\Support\Data;
use ReflectionProperty;

/**
 * Interface for attributes that handle the hydration of properties from the class.
 *
 * This enables custom property handling during the hydration process,
 * TODO: currently I don't have a use case for this, but it is here for future use.
 * so I'm not sure if the interface data is correct.
 */
interface ClassHydrationInterface
{
    /**
     * Allows attributes to modify hydration data.
     */
    public function classHydrate(ReflectionProperty $property, Data $data): void;
}
