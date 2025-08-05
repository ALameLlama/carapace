<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\ImmutableDTO;

/**
 * @method self with(array $overrides = [], string $name = null, int $age = null, bool $active = null) Creates a modified copy of the DTO with overridden values.
 */
final class TestDTO extends ImmutableDTO
{
    public function __construct(
        public string $name,
        public int $age,
        public bool $active = true,
    ) {}
}
