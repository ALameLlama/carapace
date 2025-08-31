<?php

declare(strict_types=1);

namespace Tests\Unit\Attribute;

use Alamellama\Carapace\Attributes\MapFrom;
use Alamellama\Carapace\Attributes\MapTo;
use Alamellama\Carapace\Attributes\SnakeCase;
use Alamellama\Carapace\Data;

#[SnakeCase]
class SnakeUserDTO extends Data
{
    public function __construct(
        public string $firstName,
        public string $emailAddress,
    ) {}
}

class SnakePropertyDTO extends Data
{
    public function __construct(
        #[SnakeCase]
        public string $firstName,
        public string $postalCode,
    ) {}
}

#[SnakeCase]
class SnakeOverridesDTO extends Data
{
    public function __construct(
        #[MapFrom('custom_in')]
        #[MapTo('custom_out')]
        public string $specialField,
        public string $otherField,
    ) {}
}

it('applies snake_case at class level for hydration and serialization', function (): void {
    $dto = SnakeUserDTO::from([
        'first_name' => 'Nick',
        'email_address' => 'nick@example.com',
    ]);

    expect($dto)
        ->firstName->toBe('Nick')
        ->emailAddress->toBe('nick@example.com');

    expect($dto->toArray())
        ->toHaveKey('first_name', 'Nick')
        ->toHaveKey('email_address', 'nick@example.com')
        ->not->toHaveKey('firstName')
        ->not->toHaveKey('emailAddress');
});

it('applies snake_case at property level without affecting other properties', function (): void {
    $dto = SnakePropertyDTO::from([
        'first_name' => 'Nick',
        'postalCode' => '3000',
    ]);

    expect($dto)
        ->firstName->toBe('Nick')
        ->postalCode->toBe('3000');

    expect($dto->toArray())
        ->toHaveKey('first_name', 'Nick')
        ->toHaveKey('postalCode', '3000');
});

it('respects explicit MapFrom/MapTo over class-level SnakeCase', function (): void {
    $dto = SnakeOverridesDTO::from([
        'custom_in' => 'value',
        'other_field' => 'x',
    ]);

    expect($dto)
        ->specialField->toBe('value')
        ->otherField->toBe('x');

    $array = $dto->toArray();

    expect($array)
        ->toHaveKey('custom_out', 'value') // MapTo wins
        ->toHaveKey('other_field', 'x')    // SnakeCase for remaining field
        ->not->toHaveKey('specialField')
        ->not->toHaveKey('otherField');
});

class CamelInputDTO extends Data
{
    public function __construct(
        #[SnakeCase]
        public string $firstName,
    ) {}
}

class OptionalSnakeDTO extends Data
{
    public function __construct(
        #[SnakeCase]
        public ?string $firstName = null,
    ) {}
}

it('property-level SnakeCase leaves camelCase input untouched on hydration', function (): void {
    $dto = CamelInputDTO::from([
        'firstName' => 'Nick',
    ]);

    expect($dto->firstName)->toBe('Nick');

    expect($dto->toArray())
        ->toHaveKey('first_name', 'Nick')
        ->not->toHaveKey('firstName');
});

it('property-level SnakeCase on optional property with no input produces snake_case key on output', function (): void {
    $dto = OptionalSnakeDTO::from([]);

    expect($dto->toArray())
        ->toHaveKey('first_name', null);
});

#[SnakeCase]
class OptionalSnakeClassDTO extends Data
{
    public function __construct(
        public ?string $firstName = null,
    ) {}
}

#[SnakeCase]
class CamelInputClassDTO extends Data
{
    public function __construct(
        public string $firstName,
    ) {}
}

it('class-level SnakeCase no-ops when neither camel nor snake keys are present', function (): void {
    $dto = OptionalSnakeClassDTO::from([]);

    expect($dto->toArray())
        ->toHaveKey('first_name', null);
});

it('class-level SnakeCase leaves camelCase input untouched on hydration', function (): void {
    $dto = CamelInputClassDTO::from([
        'firstName' => 'Nick',
    ]);

    expect($dto->firstName)->toBe('Nick');

    expect($dto->toArray())
        ->toHaveKey('first_name', 'Nick')
        ->not->toHaveKey('firstName');
});
