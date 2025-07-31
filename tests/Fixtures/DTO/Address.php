<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\ImmutableDTO;

/**
 * @extends ImmutableDTO<ImmutableDTO>
 */
final class Address extends ImmutableDTO
{
    public function __construct(
        public string $street,
        public string $city,
        public string $postcode,
    ) {}
}
