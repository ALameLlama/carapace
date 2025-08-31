<?php

declare(strict_types=1);

namespace Tests\Unit\Attribute;

use Alamellama\Carapace\Attributes\GroupFrom;
use Alamellama\Carapace\Data;
use Tests\Fixtures\DTO\Address;
use Tests\Fixtures\DTO\AddressReadonly;

class UserWithAddressDTO extends Data
{
    public function __construct(
        public string $name,
        #[GroupFrom('street', 'city', 'postcode')]
        public Address $address,
    ) {}
}

class UserWithOptionalAddressDTO extends Data
{
    public function __construct(
        public string $name,
        #[GroupFrom('street', 'city', 'postcode')]
        public ?Address $address,
    ) {}
}

class UserWithReadonlyAddressDTO extends Data
{
    public function __construct(
        public string $name,
        #[GroupFrom('street', 'city', 'postcode')]
        public readonly AddressReadonly $address,
    ) {}
}

it('groups flat keys into a nested DTO property', function (): void {
    $dto = UserWithAddressDTO::from([
        'name' => 'Jane Doe',
        'street' => '42 Galaxy Way',
        'city' => 'Cosmopolis',
        'postcode' => 'C0S M0S',
    ]);

    expect($dto)
        ->toBeInstanceOf(UserWithAddressDTO::class)
        ->and($dto->address)
        ->toBeInstanceOf(Address::class)
        ->and($dto->address->street)->toBe('42 Galaxy Way')
        ->and($dto->address->city)->toBe('Cosmopolis')
        ->and($dto->address->postcode)->toBe('C0S M0S');
});

it('does not override when property already present', function (): void {
    $dto = UserWithAddressDTO::from([
        'name' => 'John Smith',
        // Flat keys that should be ignored because nested address is present
        'street' => 'Ignored St',
        'city' => 'Ignored City',
        'postcode' => 'IGN 0RE',
        // Nested structure already present
        'address' => [
            'street' => '7 Beacon Hill',
            'city' => 'Harborview',
            'postcode' => 'HB1 2CD',
        ],
    ]);

    expect($dto->address->street)->toBe('7 Beacon Hill')
        ->and($dto->address->city)->toBe('Harborview')
        ->and($dto->address->postcode)->toBe('HB1 2CD');
});

it('groups flat keys into a nested readonly DTO property', function (): void {
    $dto = UserWithReadonlyAddressDTO::from([
        'name' => 'Jane Doe',
        'street' => '42 Galaxy Way',
        'city' => 'Cosmopolis',
        'postcode' => 'C0S M0S',
    ]);

    expect($dto)
        ->toBeInstanceOf(UserWithReadonlyAddressDTO::class)
        ->and($dto->address)
        ->toBeInstanceOf(AddressReadonly::class)
        ->and($dto->address->street)->toBe('42 Galaxy Way')
        ->and($dto->address->city)->toBe('Cosmopolis')
        ->and($dto->address->postcode)->toBe('C0S M0S');
});

it('does nothing when none of the source keys are present and allows null', function (): void {
    $dto = UserWithOptionalAddressDTO::from([
        'name' => 'No Address',
    ]);

    expect($dto->address)->toBeNull();
});
