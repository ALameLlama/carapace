<?php

declare(strict_types=1);

namespace Tests\Unit\Caster;

use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\DateTimeCaster;
use Alamellama\Carapace\ImmutableDTO;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

final class WithDateTimeCasting extends ImmutableDTO
{
    public function __construct(
        #[CastWith(new DateTimeCaster)]
        public DateTimeInterface $defaultFormat,

        #[CastWith(new DateTimeCaster('Y-m-d'))]
        public DateTimeInterface $customFormat
    ) {}
}

it('can cast string to DateTime using default format', function (): void {
    $dto = WithDateTimeCasting::from([
        'defaultFormat' => '2025-08-03 07:09:00',
        'customFormat' => '2025-08-03',
    ]);

    expect($dto->defaultFormat)
        ->toBeInstanceOf(DateTimeInterface::class)
        ->and($dto->defaultFormat->format('Y-m-d H:i:s'))
        ->toBe('2025-08-03 07:09:00');
});

it('can cast string to DateTime using custom format', function (): void {
    $dto = WithDateTimeCasting::from([
        'defaultFormat' => '2025-08-03 07:09:00',
        'customFormat' => '2025-08-03',
    ]);

    expect($dto->customFormat)
        ->toBeInstanceOf(DateTimeInterface::class)
        ->and($dto->customFormat->format('Y-m-d'))
        ->toBe('2025-08-03');
});

it('can cast timestamp to DateTime', function (): void {
    $timestamp = strtotime('2025-08-03 07:09:00');

    $dto = WithDateTimeCasting::from([
        'defaultFormat' => $timestamp,
        'customFormat' => '2025-08-03',
    ]);

    expect($dto->defaultFormat)
        ->toBeInstanceOf(DateTimeInterface::class)
        ->and($dto->defaultFormat->format('Y-m-d H:i:s'))
        ->toBe('2025-08-03 07:09:00');
});

it('can handle existing DateTime objects', function (): void {
    $dateTime = new DateTime('2025-08-03 07:09:00');

    $dto = WithDateTimeCasting::from([
        'defaultFormat' => $dateTime,
        'customFormat' => '2025-08-03',
    ]);

    expect($dto->defaultFormat)
        ->toBeInstanceOf(DateTimeInterface::class)
        ->and($dto->defaultFormat->format('Y-m-d H:i:s'))
        ->toBe('2025-08-03 07:09:00');
});

it('can parse date string without specific format', function (): void {
    $dto = WithDateTimeCasting::from([
        'defaultFormat' => '2025-08-03T07:09:00+00:00', // ISO 8601 format
        'customFormat' => '2025-08-03',
    ]);

    expect($dto->defaultFormat)
        ->toBeInstanceOf(DateTimeInterface::class)
        ->and($dto->defaultFormat->format('Y-m-d'))
        ->toBe('2025-08-03');
});

it('throws exception for invalid date string', function (): void {
    WithDateTimeCasting::from([
        'defaultFormat' => 'not-a-date',
        'customFormat' => '2025-08-03',
    ]);
})->throws(InvalidArgumentException::class, 'Cannot parse date string: not-a-date');

it('throws exception for unsupported type', function (): void {
    WithDateTimeCasting::from([
        'defaultFormat' => [],
        'customFormat' => '2025-08-03',
    ]);
})->throws(InvalidArgumentException::class, 'Cannot cast to DateTime: unsupported type array');
