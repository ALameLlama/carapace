<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\Data;

class User extends Data
{
    public function __construct(
        public string $name,
        #[MapFrom('email_address')]
        public string $email,
        public Address $address,
    ) {}
}
