<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\ImmutableDTO;

class NoProperty extends ImmutableDTO
{
    public function __construct(string $foo) {}
}
