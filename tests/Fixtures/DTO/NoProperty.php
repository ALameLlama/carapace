<?php

declare(strict_types=1);

namespace Tests\Fixtures\DTO;

use Alamellama\Carapace\ImmutableDTO;

/**
 * @method self with(array $overrides = []) Creates a modified copy of the DTO with overridden values.
 */
final class NoProperty extends ImmutableDTO
{
    public function __construct(string $foo) {}
}
