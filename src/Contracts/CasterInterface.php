<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Contracts;

/**
 * Interface for value type casting.
 *
 * This interface defines the contract for classes that handle
 * converting values from one type to another. Implementations
 * should handle type conversion logic and the appropriate error handling.
 */
interface CasterInterface
{
    /**
     * Cast a value to a specific type.
     *
     * @param  mixed  $value  The value to be cast
     * @return mixed The cast value
     */
    public function cast(mixed $value): mixed;
}
