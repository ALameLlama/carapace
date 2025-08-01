<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

/**
 * Interface for attributes that handle hydration of properties.
 * This allows for custom handling of properties during the hydration process.
 * TODO: This interface is not yet implemented.
 */
interface HandlesHydration
{
    /**
     * Allows attributes to modify hydration data.
     *
     * @param  string  $propertyName  The property name being handled.
     * @param  array<mixed>  $data  The data being hydrated.
     */
    public function handle(string $propertyName, array &$data): void;
}
