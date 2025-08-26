<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\Data;

readonly class WithDefaultValue extends Data
{
    public function __construct(
        public string $name = 'Default Nick',
    ) {}
}
