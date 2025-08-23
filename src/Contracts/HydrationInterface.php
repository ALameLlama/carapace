<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Contracts;

use Alamellama\Carapace\Support\Data;

/**
 * Interface for attributes that handle the hydration of properties.
 *
 * This enables custom property handling during the hydration process,
 * TODO: currently I don't have a use case for this, but it is here for future use.
 * so I'm not sure if the interface data is correct.
 */
interface HydrationInterface
{
    /**
     * Allows attributes to modify hydration data.
     *
     * @param  string  $propertyName  The property name being handled.
     */
    public function handle(string $propertyName, Data $data): void;
}
