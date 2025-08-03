<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Casters;

use Alamellama\Carapace\Contracts\CasterInterface;
use InvalidArgumentException;

/**
 * Caster for primitive PHP types.
 *
 * This class handles casting values to primitive PHP types (int, float, string, bool).
 * It implements the CasterInterface and provides type conversion with error handling.
 */
final readonly class PrimitiveCaster implements CasterInterface
{
    /**
     * Create a new primitive type caster.
     *
     * @param  string  $type  The primitive type to cast to ('int', 'float', 'string', 'bool')
     */
    public function __construct(
        private string $type
    ) {}

    /**
     * Cast a value to the specified primitive type.
     *
     * @param  mixed  $value  The value to be cast
     * @return mixed The cast value (int, float, string, or bool)
     *
     * @throws InvalidArgumentException If the type is not supported or casting fails
     */
    public function cast(mixed $value): mixed
    {
        return match ($this->type) {
            'int' => (int) $value, // @phpstan-ignore-line
            'float' => (float) $value, // @phpstan-ignore-line
            'string' => (string) $value, // @phpstan-ignore-line:
            'bool' => (bool) $value,
            default => throw new InvalidArgumentException("Unsupported primitive type: {$this->type}"),
        };
    }
}
