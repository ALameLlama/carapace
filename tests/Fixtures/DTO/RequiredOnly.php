<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\Data;

class RequiredOnly extends Data
{
    public function __construct(
        public string $required,
    ) {}
}
