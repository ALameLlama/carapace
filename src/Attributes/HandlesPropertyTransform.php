<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

interface HandlesPropertyTransform
{
    /**
     * Allows attributes to modify property when transforming to array.
     *
     * @return array{key: string, value: mixed}
     */
    public function handle(string $propertyName, mixed $value): array;
}
