<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

/**
 * Interface for attributes that handle property transformations when converting to an array.
 * This allows for custom handling of properties during the transformation process.
 * e.g. MapTo
 */
interface HandlesPropertyTransform
{
    /**
     * Allows attributes to modify property when transforming to array.
     *
     * @return array{key: string, value: mixed}
     */
    public function handle(string $propertyName, mixed $value): array;
}
