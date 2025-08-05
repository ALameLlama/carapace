<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\ImmutableDTO;

/**
 * @method self with(array $overrides = [], ?string $optional = null) Creates a modified copy of the DTO with overridden values.
 */
final class Nullable extends ImmutableDTO
{
    public function __construct(
        public ?string $optional,
    ) {}
}
