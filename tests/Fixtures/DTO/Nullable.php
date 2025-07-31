<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\ImmutableDTO;

final class Nullable extends ImmutableDTO
{
    public function __construct(
        public ?string $optional,
    ) {}
}
