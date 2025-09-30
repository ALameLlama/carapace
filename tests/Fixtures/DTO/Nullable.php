<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\Data;

class Nullable extends Data
{
    public function __construct(
        public ?string $optional,
    ) {}
}
