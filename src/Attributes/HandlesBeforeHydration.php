<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

/**
 * Interface for attributes that handle modifications to hydration data before it is applied to the object.
 * This allows for custom handling of properties during the hydration process.
 * e.g CastWith, MapFrom
 */
interface HandlesBeforeHydration
{
    /**
     * Allows attributes to modify hydration data as needed.
     *
     * @param  string  $propertyName  The property name being handled.
     * @param  array<mixed>  $data  The data being hydrated.
     */
    public function handleBeforeHydration(string $propertyName, array &$data): void;
}
