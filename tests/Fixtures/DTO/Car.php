<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\Data;

class Car extends Data
{
    public function __construct(
        public string $make,
        public string $model,
        public int $year,
        public ?string $color = null
    ) {}
}
