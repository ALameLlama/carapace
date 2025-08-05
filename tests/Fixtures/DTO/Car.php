<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\ImmutableDTO;

/**
 * @method self with(array $overrides = [], string $make = null, string $model = null, int $year = null, ?string $color = null) Creates a modified copy of the DTO with overridden values.
 */
final class Car extends ImmutableDTO
{
    public function __construct(
        public string $make,
        public string $model,
        public int $year,
        public ?string $color = null
    ) {}
}
