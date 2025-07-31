<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\ImmutableDTO;

final class RequiredOnly extends ImmutableDTO
{
    public function __construct(
        public string $required,
    ) {}
}
