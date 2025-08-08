<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Casters;

use Alamellama\Carapace\Contracts\CasterInterface;
use InvalidArgumentException;

use function is_array;
use function is_object;
use function is_string;

/**
 * Caster for primitive PHP types.
 *
 * This class handles casting values to primitive PHP types (int, float, string, bool, array).
 * It implements the CasterInterface and provides type conversion with error handling.
 */
final readonly class PrimitiveCaster implements CasterInterface
{
    /**
     * Create a new primitive type caster.
     *
     * @param  string  $type  The primitive type to cast to ('int', 'float', 'string', 'bool', 'array')
     */
    public function __construct(
        private string $type
    ) {}

    /**
     * Cast a value to the specified primitive type.
     *
     * @param  mixed  $value  The value to be cast
     * @return mixed The cast value (int, float, string, bool, or array)
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
            'array' => match (true) {
                is_array($value) => $value,
                is_string($value) && $this->isValidJson($value) => json_decode($value, true),
                is_object($value) => (array) $value,
                default => [$value],
            },
            default => throw new InvalidArgumentException("Unsupported primitive type: {$this->type}"),
        };
    }

    /**
     * Check if a string is valid JSON.
     *
     * @param  string  $string  The string to check
     * @return bool True if the string is valid JSON, false otherwise
     */
    private function isValidJson(string $string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
