<?php

declare(strict_types=1);

namespace Alamellama\Carapace;

use Alamellama\Carapace\Contracts\DTOInterface;
use Alamellama\Carapace\Traits\DTOTrait;

/**
 * Immutable Data Transfer Object (DTO) Base Class.
 */
abstract readonly class ImmutableData implements DTOInterface
{
    use DTOTrait;
}
