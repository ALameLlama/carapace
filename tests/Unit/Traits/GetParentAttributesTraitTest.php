<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\Attributes\MapTo;
use Alamellama\Carapace\Attributes\SnakeCase;
use Alamellama\Carapace\Data;
use Attribute;
use RuntimeException;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class UnrelatedThrowingAttribute
{
    public function __construct()
    {
        throw new RuntimeException('Unrelated attributes must not be instantiated');
    }
}

#[SnakeCase]
class BaseDTO extends Data {}

class FirstNameDTO extends BaseDTO
{
    public function __construct(
        public string $firstName
    ) {}
}

#[SnakeCase]
#[UnrelatedThrowingAttribute]
class FilteredAttributesDTO extends Data
{
    public function __construct(
        #[MapFrom('source_name')]
        #[MapTo('destination_name')]
        #[UnrelatedThrowingAttribute]
        public string $displayName,
        public string $firstName,
    ) {}
}

it('can support nested DTO extending a BaseDTO camelCase', function (): void {
    $dto = FirstNameDTO::from([
        'firstName' => 'Nick',
    ]);

    expect($dto->firstName)
        ->toBe('Nick');

    expect(json_decode($dto->toJson(), true))
        ->toHaveKey('first_name')
        ->first_name->toBe('Nick');
});

it('can support nested DTO extending a BaseDTO SnakeCase', function (): void {
    $dto = FirstNameDTO::from([
        'first_name' => 'Nick',
    ]);

    expect($dto->firstName)
        ->toBe('Nick');

    expect(json_decode($dto->toJson(), true))
        ->toHaveKey('first_name')
        ->first_name->toBe('Nick');
});

it('does not instantiate unrelated class or property attributes', function (): void {
    $dto = FilteredAttributesDTO::from([
        'source_name' => 'Display',
        'first_name' => 'Nick',
    ]);

    expect($dto)
        ->displayName->toBe('Display')
        ->firstName->toBe('Nick');

    expect($dto->toArray())
        ->toHaveKey('destination_name', 'Display')
        ->toHaveKey('first_name', 'Nick');
});
