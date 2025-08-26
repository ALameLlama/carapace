<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\Data;

readonly class Address extends Data
{
    public function __construct(
        public string $street,
        public string $city,
        public string $postcode,
    ) {}
}
