<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Interfaces;

/**
 * Interface for attributes that handle property transformations when converting to an array.
 * This allows for custom handling of properties d
 * during the transformation process.
 * e.g. MapTo
 */
interface TransformationHandler
{
    /**
     * Allows attributes to modify property when transforming to array.
     *
     * @return array{key: string, value: mixed}
     */
    public function handle(string $propertyName, mixed $value): array;
}
