<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

/**
 * Generic interface for attributes to modify hydration data.
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
