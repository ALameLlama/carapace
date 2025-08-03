<?php

declare(strict_types=1);

namespace Alamellama\Carapace\Casters;

use Alamellama\Carapace\Contracts\CasterInterface;
use DateTime;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;

/**
 * Caster for DateTime objects.
 *
 * This class handles casting values to DateTime objects.
 * It implements the CasterInterface and provides flexible DateTime conversion.
 */
final readonly class DateTimeCaster implements CasterInterface
{
    /**
     * Create a new DateTime caster.
     *
     * @param  string  $format  The format to use when parsing date strings (default: 'Y-m-d H:i:s')
     */
    public function __construct(
        private string $format = 'Y-m-d H:i:s'
    ) {}

    /**
     * Cast a value to a DateTime object.
     *
     * @param  mixed  $value  The value to be cast (string, int, or DateTime)
     * @return DateTimeInterface The cast DateTime object
     *
     * @throws InvalidArgumentException If the value cannot be cast to DateTime
     */
    public function cast(mixed $value): DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_string($value)) {
            $dateTime = DateTime::createFromFormat($this->format, $value);

            if ($dateTime !== false) {
                return $dateTime;
            }

            try {
                return new DateTime($value);
            } catch (Exception $e) {
                throw new InvalidArgumentException("Cannot parse date string: {$value}", 0, $e);
            }
        }

        if (is_int($value)) {
            $dateTime = new DateTime;
            $dateTime->setTimestamp($value);

            return $dateTime;
        }

        throw new InvalidArgumentException('Cannot cast to DateTime: unsupported type ' . gettype($value));
    }
}
