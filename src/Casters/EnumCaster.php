<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Casters;

use Alamellama\Carapace\Contracts\CasterInterface;
use BackedEnum;
use InvalidArgumentException;
use UnitEnum;
use ValueError;

/**
 * Caster for native PHP Enums.
 *
 * This class handles casting values to PHP enum types.
 * It implements the CasterInterface and provides conversion for both backed and unit enums.
 */
final readonly class EnumCaster implements CasterInterface
{
    /**
     * Create a new enum caster.
     *
     * @param  string  $enumClass  The fully qualified name of the enum class
     */
    public function __construct(
        private string $enumClass
    ) {}

    /**
     * Cast a value to the specified enum type.
     *
     * @param  mixed  $value  The value to be cast
     * @return UnitEnum The cast enum instance
     *
     * @throws InvalidArgumentException If the value cannot be cast to the enum
     */
    public function cast(mixed $value): UnitEnum
    {
        // If already an instance of the target enum, return it
        if ($value instanceof $this->enumClass && $value instanceof UnitEnum) {
            return $value;
        }

        // Check if the enum class exists
        if (! enum_exists($this->enumClass)) {
            throw new InvalidArgumentException("Invalid enum class: {$this->enumClass}");
        }

        // Handle backed enums
        if (is_subclass_of($this->enumClass, BackedEnum::class)) {
            // Try to get the enum case from its value
            try {
                /** @var int|string $value */
                return $this->enumClass::from($value);
            } catch (ValueError $e) {
                // If the exact value doesn't exist, try to find a case that matches case-insensitively
                if (is_string($value)) {
                    foreach ($this->enumClass::cases() as $case) {
                        if (strcasecmp((string) $case->value, $value) === 0) {
                            return $case;
                        }
                    }
                }

                // Use tryFrom as a fallback
                if ($value !== null) {
                    $result = $this->enumClass::tryFrom($value);
                    if ($result !== null) {
                        // @codeCoverageIgnoreStart
                        return $result;
                        // @codeCoverageIgnoreEnd
                    }
                }

                throw new InvalidArgumentException(
                    "Cannot cast value to enum {$this->enumClass}: {$e->getMessage()}",
                    $e->getCode(),
                    $e
                );
            }
        }

        // Handle unit enums (enums without values)
        // For unit enums, we need to match by name
        if (is_string($value)) {
            foreach ($this->enumClass::cases() as $case) {
                if ($case->name === $value || strcasecmp($case->name, $value) === 0) {
                    return $case;
                }
            }
        }

        throw new InvalidArgumentException(
            "Cannot cast value to enum {$this->enumClass}: no matching case found"
        );
    }
}
