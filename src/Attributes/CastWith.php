<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class CastWith implements HandlesPropertyValue
{
    public function __construct(
        public string $casterClass
    ) {}

    public function handle(mixed $value, ?array $data = null): mixed
    {
        if (is_array($value)) {
            return array_map(
                fn ($item) => $item instanceof $this->casterClass ? $item : $this->casterClass::from($item),
                $value
            );
        }

        return $value instanceof $this->casterClass ? $value : $this->casterClass::from($value);
    }
}
