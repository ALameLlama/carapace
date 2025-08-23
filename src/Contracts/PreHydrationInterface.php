<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Contracts;

use Alamellama\Carapace\Support\Data;

/**
 * Interface for attributes that handle modifications to hydration data before it is applied to the object.
 *
 * This enables custom handling of properties before the hydration process.
 * e.g. CastWith, MapFrom
 */
interface PreHydrationInterface
{
    /**
     * Allows attributes to modify hydration data as needed.
     *
     * @param  string  $propertyName  The property name being handled.
     */
    public function handle(string $propertyName, Data $data): void;
}
