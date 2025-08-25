<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\ImmutableDTO;

class WithDefaultValue extends ImmutableDTO
{
    public function __construct(
        public string $name = 'Default Nick',
    ) {}
}
