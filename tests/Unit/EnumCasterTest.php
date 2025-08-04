<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\EnumCaster;
use Alamellama\Carapace\ImmutableDTO;
use InvalidArgumentException;
use Tests\Fixtures\Enums\Color;
use Tests\Fixtures\Enums\Status;

final class WithEnumCasting extends ImmutableDTO
{
    public function __construct(
        #[CastWith(new EnumCaster(Status::class))]
        public Status $status,

        #[CastWith(new EnumCaster(Color::class))]
        public Color $color
    ) {}
}

it('can cast string to backed enum using exact value', function (): void {
    $dto = WithEnumCasting::from([
        'status' => 'active',
        'color' => 'RED',
    ]);

    expect($dto->status)
        ->toBe(Status::ACTIVE)
        ->and($dto->color)
        ->toBe(Color::RED);
});

it('can cast string to backed enum using case-insensitive value', function (): void {
    $dto = WithEnumCasting::from([
        'status' => 'ACTIVE',
        'color' => 'RED',
    ]);

    expect($dto->status)
        ->toBe(Status::ACTIVE);
});

it('can handle existing enum instances', function (): void {
    $dto = WithEnumCasting::from([
        'status' => Status::PENDING,
        'color' => Color::GREEN,
    ]);

    expect($dto->status)
        ->toBe(Status::PENDING)
        ->and($dto->color)
        ->toBe(Color::GREEN);
});

it('can cast string to unit enum using case name', function (): void {
    $dto = WithEnumCasting::from([
        'status' => 'active',
        'color' => 'BLUE',
    ]);

    expect($dto->color)
        ->toBe(Color::BLUE);
});

it('can cast string to unit enum using case-insensitive name', function (): void {
    $dto = WithEnumCasting::from([
        'status' => 'active',
        'color' => 'blue',
    ]);

    expect($dto->color)
        ->toBe(Color::BLUE);
});

it('throws exception for invalid backed enum value', function (): void {
    WithEnumCasting::from([
        'status' => 'unknown',
        'color' => 'RED',
    ]);
})->throws(InvalidArgumentException::class, 'Cannot cast value to enum Tests\Fixtures\Enums\Status');

it('throws exception for invalid unit enum name', function (): void {
    WithEnumCasting::from([
        'status' => 'active',
        'color' => 'YELLOW',
    ]);
})->throws(InvalidArgumentException::class, 'Cannot cast value to enum Tests\Fixtures\Enums\Color: no matching case found');

it('throws exception for invalid enum class', function (): void {
    $caster = new EnumCaster('NonExistentEnum');
    $caster->cast('test');
})->throws(InvalidArgumentException::class, 'Invalid enum class: NonExistentEnum');

it('throws exception for unsupported value type with unit enum', function (): void {
    $caster = new EnumCaster(Color::class);
    $caster->cast(123);
})->throws(InvalidArgumentException::class, 'Cannot cast value to enum Tests\Fixtures\Enums\Color: no matching case found');
