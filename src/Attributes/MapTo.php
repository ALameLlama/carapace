<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class MapTo implements HandlesPropertyTransform
{
    public function __construct(
        public string $destinationKey
    ) {}

    public function handle(string $propertyName, mixed $value): array
    {
        return ['key' => $this->destinationKey, 'value' => $value];
    }
}
