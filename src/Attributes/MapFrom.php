<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class MapFrom implements HandlesBeforeHydration
{
    public function __construct(public string $sourceKey) {}

    public function handleBefore(string $propertyName, array &$data): void
    {
        if (array_key_exists($this->sourceKey, $data)) {
            $data[$propertyName] = $data[$this->sourceKey];
        }
    }
}
