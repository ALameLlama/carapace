<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Contracts;

/**
 * Interface for attributes that handle property transformations when converting to an array.
 *
 * This enable custom handling of properties during the transformation process.
 * e.g. MapTo
 */
interface TransformationHandler
{
    /**
     * Allows attributes to modify property when transforming to array.
     *
     * @return array{string, mixed}
     */
    public function handle(string $propertyName, mixed $value): array;
}
