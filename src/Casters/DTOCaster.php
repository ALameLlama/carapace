<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Casters;

use Alamellama\Carapace\Contracts\CasterInterface;
use Alamellama\Carapace\ImmutableDTO;
use InvalidArgumentException;

use function array_is_list;
use function is_array;
use function is_object;
use function is_string;

/**
 * Caster that converts arrays/objects into DTO instances (or arrays of DTOs).
 *
 * It supports:
 * - Passing through if value is already an instance of the target DTO
 * - Casting an associative array/object via TargetDTO::from
 * - Casting a list (array_is_list) of items, mapping each via TargetDTO::from
 */
final readonly class DTOCaster implements CasterInterface
{
    /** @param class-string<ImmutableDTO> $dtoClass */
    public function __construct(public string $dtoClass) {}

    /**
     * Exposes the target DTO class-string for error context.
     *
     * @return class-string<ImmutableDTO>
     */
    public function targetClass(): string
    {
        return $this->dtoClass;
    }

    /**
     * @return ImmutableDTO|array<int, ImmutableDTO>
     */
    public function cast(mixed $value): mixed
    {
        if ($value instanceof $this->dtoClass) {
            return $value;
        }

        // List of DTO payloads
        if (is_array($value) && array_is_list($value)) {
            return array_map(
                function ($item) {
                    if ($item instanceof $this->dtoClass) {
                        return $item;
                    }
                    if (is_array($item) || is_object($item) || is_string($item)) {
                        /** @var array<mixed,mixed>|object|string $item */
                        return $this->dtoClass::from($item);
                    }

                    throw new InvalidArgumentException("Cannot cast list item to DTO {$this->dtoClass}: unsupported input");
                },
                $value
            );
        }

        // Single DTO payload (assoc array, object, or JSON string)
        if (is_array($value) || is_object($value) || is_string($value)) {
            /** @var array<mixed,mixed>|object|string $value */
            return $this->dtoClass::from($value);
        }

        throw new InvalidArgumentException("Cannot cast value to DTO {$this->dtoClass}: unsupported input");
    }
}
