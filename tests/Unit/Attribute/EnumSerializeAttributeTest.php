<?php

declare(strict_types=1);

namespace Tests\Unit\Attribute;

use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Attributes\EnumSerialize;
use Alamellama\Carapace\Casters\EnumCaster;
use Alamellama\Carapace\Data;
use Tests\Fixtures\Enums\Color;
use Tests\Fixtures\Enums\Status;
use Tests\Fixtures\Enums\StatusCode;

readonly class EnumNameDto extends Data
{
    public function __construct(
        #[CastWith(new EnumCaster(Status::class))]
        #[EnumSerialize(EnumSerialize::NAME)]
        public Status $status,

        #[CastWith(new EnumCaster(Color::class))]
        #[EnumSerialize(EnumSerialize::NAME)]
        public Color $color,
    ) {}
}

readonly class EnumValueDto extends Data
{
    public function __construct(
        #[CastWith(new EnumCaster(Status::class))]
        #[EnumSerialize(EnumSerialize::VALUE)]
        public Status $status,

        #[CastWith(new EnumCaster(StatusCode::class))]
        #[EnumSerialize(EnumSerialize::VALUE)]
        public StatusCode $code,
    ) {}
}

readonly class EnumMethodDto extends Data
{
    public function __construct(
        #[CastWith(new EnumCaster(Status::class))]
        #[EnumSerialize(method: 'description')]
        public Status $status,
    ) {}
}

readonly class EnumUnitWithValueDto extends Data
{
    public function __construct(
        #[CastWith(new EnumCaster(Color::class))]
        #[EnumSerialize(EnumSerialize::VALUE)]
        public Color $color,
    ) {}
}

readonly class EnumNonExistingMethodDto extends Data
{
    public function __construct(
        #[CastWith(new EnumCaster(Color::class))]
        #[EnumSerialize(method: 'nonExistingMethod')]
        public Color $color,

        // Misused attribute on a non-enum property should no-op
        #[EnumSerialize(EnumSerialize::VALUE)]
        public string $label = 'hello',
    ) {}
}

it('serializes enums using name', function (): void {
    $dto = EnumNameDto::from([
        'status' => 'active',
        'color' => 'RED',
    ]);

    expect($dto->toArray())
        ->toMatchArray([
            'status' => 'ACTIVE',
            'color' => 'RED',
        ]);
});

it('serializes enums using value', function (): void {
    $dto = EnumValueDto::from([
        'status' => 'active',
        'code' => 200,
    ]);

    expect($dto->toArray())
        ->toMatchArray([
            'status' => 'active',
            'code' => 200,
        ]);
});

it('serializes enums using custom method', function (): void {
    $dto = EnumMethodDto::from([
        'status' => 'pending',
    ]);

    expect($dto->toArray())
        ->toMatchArray([
            'status' => 'Pending Approval',
        ]);
});

it('falls back to name for unit enums when value  is requested', function (): void {
    $dto = EnumUnitWithValueDto::from([
        'color' => 'GREEN',
    ]);

    expect($dto->toArray())
        ->toMatchArray([
            'color' => 'GREEN',
        ]);
});

it('ignores non-existing custom method and uses default', function (): void {
    $dto = EnumNonExistingMethodDto::from([
        'color' => 'BLUE',
    ]);

    expect($dto->toArray())
        ->toMatchArray([
            'color' => 'BLUE',
            'label' => 'hello',
        ]);
});

readonly class EnumUnitMethodDto extends Data
{
    public function __construct(
        #[CastWith(new EnumCaster(Color::class))]
        #[EnumSerialize(method: 'niceName')]
        public Color $color,
    ) {}
}

it('serializes non-backed enums using a custom method', function (): void {
    $dto = EnumUnitMethodDto::from([
        'color' => 'RED',
    ]);

    expect($dto->toArray())
        ->toMatchArray([
            'color' => 'Red',
        ]);
});
