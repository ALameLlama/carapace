<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\ImmutableDTO;

final class User extends ImmutableDTO
{
    public function __construct(
        public string $name,
        #[MapFrom('email_address')]
        public string $email,
        public Address $address,
    ) {}
}
