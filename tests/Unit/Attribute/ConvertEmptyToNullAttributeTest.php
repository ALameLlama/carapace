<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\ConvertEmptyToNull;
use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\Data;

class ConvertEmptyPropertyDTO extends Data
{
    public function __construct(
        #[ConvertEmptyToNull]
        public ?string $name,
        #[ConvertEmptyToNull]
        public ?array $tags,
        public bool $required,
    ) {}
}

it('converts empty string and empty array to null on property level for nullable properties', function (): void {
    $dto = ConvertEmptyPropertyDTO::from([
        'name' => '',
        'tags' => [],
        'required' => true,
    ]);

    expect($dto)
        ->name->toBeNull()
        ->tags->toBeNull()
        ->required->toBeTrue();
});

it('does not convert empties when property is non-nullable', function (): void {
    $dto = ConvertEmptyPropertyDTO::from([
        'name' => 'ok',
        'tags' => [],
        'required' => false,
    ]);

    expect($dto)
        ->name->toBe('ok')
        ->tags->toBeNull()
        ->required->toBeFalse();
});

#[ConvertEmptyToNull]
class ConvertEmptyClassDTO extends Data
{
    public function __construct(
        public ?string $name,
        public ?array $tags,
        public bool $required,
    ) {}
}

it('applies class-level ConvertEmptyToNull to all nullable properties', function (): void {
    $dto = ConvertEmptyClassDTO::from([
        'name' => '',
        'tags' => [],
        'required' => true,
    ]);

    expect($dto)
        ->name->toBeNull()
        ->tags->toBeNull()
        ->required->toBeTrue();
});

class ConvertEmptyMapFromDTO extends Data
{
    public function __construct(
        #[MapFrom('source_name')]
        #[ConvertEmptyToNull]
        public ?string $name,
    ) {}
}

it('works with MapFrom before conversion', function (): void {
    $dto = ConvertEmptyMapFromDTO::from([
        'source_name' => '',
    ]);

    expect($dto->name)->toBeNull();
});

class OptionalPropertyConvertEmptyDTO extends Data
{
    public function __construct(
        #[ConvertEmptyToNull]
        public ?string $name = null,
    ) {}
}

it('leaves default null on property-level when key is missing', function (): void {
    $dto = OptionalPropertyConvertEmptyDTO::from([]);

    expect($dto->toArray())
        ->toHaveKey('name', null);
});

#[ConvertEmptyToNull]
class OptionalClassConvertEmptyDTO extends Data
{
    public function __construct(
        public ?string $name = null,
    ) {}
}

it('leaves default null on class-level when key is missing', function (): void {
    $dto = OptionalClassConvertEmptyDTO::from([]);

    expect($dto->toArray())
        ->toHaveKey('name', null);
});

it('works when passing in null', function (): void {
    $dto = OptionalClassConvertEmptyDTO::from([
        'name' => null,
    ]);

    expect($dto->toArray())
        ->toHaveKey('name', null);
});
