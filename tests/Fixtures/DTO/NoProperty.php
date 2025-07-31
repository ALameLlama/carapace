<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\ImmutableDTO;

final class NoProperty extends ImmutableDTO
{
    public function __construct(string $foo) {}
}
