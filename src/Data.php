<?php

declare(strict_types=1);

namespace Alamellama\Carapace;

use Alamellama\Carapace\Contracts\DTOInterface;
use Alamellama\Carapace\Traits\DTOTrait;

/**
 * Data Transfer Object (DTO) Base Class.
 */
abstract class Data implements DTOInterface
{
    use DTOTrait;
}
