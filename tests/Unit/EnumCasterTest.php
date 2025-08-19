<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\EnumCaster;
use Alamellama\Carapace\ImmutableDTO;
use InvalidArgumentException;
use Tests\Fixtures\Enums\Color;
use Tests\Fixtures\Enums\Status;
use Tests\Fixtures\Enums\StatusCode;

final class StatusDto extends ImmutableDTO
{
    public function __construct(
        #[CastWith(new EnumCaster(Status::class))]
        public Status $status,
    ) {}
}

final class ColorDto extends ImmutableDTO
{
    public function __construct(
        #[CastWith(new EnumCaster(Color::class))]
        public Color $color,
    ) {}
}

final class StatusCodeDto extends ImmutableDTO
{
    public function __construct(
        #[CastWith(new EnumCaster(StatusCode::class))]
        public StatusCode $statusCode,
    ) {}
}

final class CombinedDto extends ImmutableDTO
{
    public function __construct(
        #[CastWith(new EnumCaster(Status::class))]
        public Status $status,

        #[CastWith(new EnumCaster(StatusCode::class))]
        public ?StatusCode $statusCode,
    ) {}
}

it('can cast string to backed enum using exact value', function (): void {
    $dto = StatusDto::from([
        'status' => 'active',
    ]);

    expect($dto->status)
        ->toBe(Status::ACTIVE);
});

it('can cast string to backed enum using case-insensitive value', function (): void {
    $dto = StatusDto::from([
        'status' => 'ACTIVE',
    ]);

    expect($dto->status)
        ->toBe(Status::ACTIVE);
});

it('can cast int backed enum using int value', function (): void {
    $dto = StatusCodeDto::from([
        'statusCode' => 100,
    ]);

    expect($dto->statusCode)
        ->toBe(StatusCode::PENDING);
});

it('can cast int backed enum using string value', function (): void {
    $dto = StatusCodeDto::from([
        'statusCode' => '300',
    ]);

    expect($dto->statusCode)
        ->toBe(StatusCode::INACTIVE);
});

it('can handle existing enum instances', function (): void {
    $dto1 = StatusDto::from([
        'status' => Status::PENDING,
    ]);

    $dto2 = ColorDto::from([
        'color' => Color::GREEN,
    ]);

    expect($dto1->status)
        ->toBe(Status::PENDING)
        ->and($dto2->color)
        ->toBe(Color::GREEN);
});

it('can cast string to unit enum using case name', function (): void {
    $dto = ColorDto::from([
        'status' => 'active',
        'color' => 'BLUE',
    ]);

    expect($dto->color)
        ->toBe(Color::BLUE);
});

it('can cast string to unit enum using case-insensitive name', function (): void {
    $dto = ColorDto::from([
        'color' => 'blue',
    ]);

    expect($dto->color)
        ->toBe(Color::BLUE);
});

it('throws exception for invalid backed enum value', function (): void {
    StatusDto::from([
        'status' => 'unknown',
    ]);
})->throws(InvalidArgumentException::class, 'Cannot cast value to enum Tests\Fixtures\Enums\Status');

it('throws exception for invalid unit enum name', function (): void {
    ColorDto::from([
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

it('can handle optional property', function (): void {
    $dto = CombinedDto::from([
        'status' => Status::PENDING,
    ]);

    expect($dto->status)
        ->toBe(Status::PENDING);

    expect($dto->statusCode)
        ->toBeNull();
});

it('can handle null property', function (): void {
    $dto = CombinedDto::from([
        'status' => Status::PENDING,
        'statusCode' => null,
    ]);

    expect($dto->status)
        ->toBe(Status::PENDING);

    expect($dto->statusCode)
        ->toBeNull();
});
