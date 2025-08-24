<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Casters;

use Alamellama\Carapace\Contracts\CasterInterface;
use BackedEnum;
use InvalidArgumentException;
use ReflectionEnum;
use UnitEnum;
use ValueError;

use function is_string;

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
        if ($value instanceof $this->enumClass && $value instanceof UnitEnum) {
            return $value;
        }

        if (! enum_exists($this->enumClass)) {
            throw new InvalidArgumentException("Invalid enum class: {$this->enumClass}");
        }

        if (is_subclass_of($this->enumClass, BackedEnum::class)) {
            try {
                $reflectedEnum = new ReflectionEnum($this->enumClass);

                // If the backing type is int, cast to int, otherwise cast to string
                // @phpstan-ignore-next-line
                $value = $reflectedEnum->getBackingType()?->getName() === 'int' ? (int) $value : (string) $value;

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
                $result = $this->enumClass::tryFrom($value);
                if ($result !== null) {
                    // @codeCoverageIgnoreStart
                    return $result;
                    // @codeCoverageIgnoreEnd
                }

                throw new InvalidArgumentException(
                    "Cannot cast value to enum {$this->enumClass}: {$e->getMessage()}",
                    $e->getCode(),
                    $e
                );
            }
        }

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
