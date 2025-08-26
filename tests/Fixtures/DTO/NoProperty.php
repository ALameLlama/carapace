<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\Data;

class NoProperty extends Data
{
    public function __construct(string $foo) {}
}
