<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\ImmutableDTO;

/**
 * @method self with(array $overrides = [], string $name = null, string $email = null, Tests\Fixtures\DTO\Address $address = null) Creates a modified copy of the DTO with overridden values.
 */
final class User extends ImmutableDTO
{
    public function __construct(
        public string $name,
        #[MapFrom('email_address')]
        public string $email,
        public Address $address,
    ) {}
}
