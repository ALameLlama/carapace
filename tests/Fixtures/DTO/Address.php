<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\ImmutableDTO;

/**
 * @method self with(array $overrides = [], string $street = null, string $city = null, string $postcode = null) Creates a modified copy of the DTO with overridden values.
 */
final class Address extends ImmutableDTO
{
    public function __construct(
        public string $street,
        public string $city,
        public string $postcode,
    ) {}
}
