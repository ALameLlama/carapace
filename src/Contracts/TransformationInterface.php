<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Contracts;

/**
 * Interface for attributes that handle property transformations when converting to an array.
 *
 * This enables custom handling of properties during the transformation process.
 * e.g. MapTo
 */
interface TransformationInterface
{
    /**
     * Allows attributes to modify property when transforming to array.
     *
     * @return array{string, mixed}
     */
    public function handle(string $propertyName, mixed $value): array;
}
