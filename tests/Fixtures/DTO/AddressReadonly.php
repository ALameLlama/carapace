<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\Data;

class AddressReadonly extends Data
{
    public function __construct(
        public readonly string $street,
        public readonly string $city,
        public readonly string $postcode,
    ) {}
}
