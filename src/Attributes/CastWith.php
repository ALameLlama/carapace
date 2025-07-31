<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class CastWith
{
    public function __construct(
        public string $casterClass
    ) {}
}
