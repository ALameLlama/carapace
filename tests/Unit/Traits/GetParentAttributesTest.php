<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Attributes\SnakeCase;
use Alamellama\Carapace\Casters\DateTimeCaster;
use Alamellama\Carapace\ImmutableDTO;
use DateTimeInterface;

#[SnakeCase]
class BaseDTO extends ImmutableDTO {}

class CreateAtDTO extends BaseDTO
{
    public function __construct(
        #[CastWith(DateTimeCaster::class)]
        public DateTimeInterface $createdAt
    ) {}
}

it('can support nested DTO extending a BaseDTO camelCase', function (): void {
    $dto = CreateAtDTO::from([
        'createdAt' => '2025-08-03',
    ]);

    expect($dto->createdAt)
        ->toBeInstanceOf(DateTimeInterface::class)
        ->and($dto->createdAt->format('Y-m-d'))
        ->toBe('2025-08-03');
});

it('can support nested DTO extending a BaseDTO SnakeCase', function (): void {
    $dto = CreateAtDTO::from([
        'created_at' => '2025-08-03',
    ]);

    expect($dto->createdAt)
        ->toBeInstanceOf(DateTimeInterface::class)
        ->and($dto->createdAt->format('Y-m-d'))
        ->toBe('2025-08-03');
});
