<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\CastWith;
use Alamellama\Carapace\Casters\DateTimeCaster;
use Alamellama\Carapace\Support\Data;
use InvalidArgumentException;
use Tests\Fixtures\DTO\Address;
use Tests\Fixtures\DTO\User;

it('can cast class-string of DTO instances', function (): void {
    $attribute = new CastWith(User::class);

    expect($attribute->caster)
        ->toBeString(User::class);
});

it('can cast class-string of caster interface', function (): void {
    $attribute = new CastWith(DateTimeCaster::class);

    expect($attribute->caster)
        ->toBeInstanceOf(DateTimeCaster::class);
});

it('can cast caster interface', function (): void {
    $attribute = new CastWith(new DateTimeCaster);

    expect($attribute->caster)
        ->toBeInstanceOf(DateTimeCaster::class);
});

it('can cast nested arrays into DTO instances', function (): void {
    $data = [
        'users' => [
            [
                'name' => 'Nick',
                'email' => 'nick@example.com',
                'address' => [
                    'street' => '123 Main St',
                    'city' => 'Melbourne',
                    'postcode' => '3000',
                ],
            ],
            [
                'name' => 'Mike',
                'email' => 'mike@example.com',
                'address' => [
                    'street' => '123 Main St',
                    'city' => 'Melbourne',
                    'postcode' => '3000',
                ],
            ],
        ],
    ];

    $attribute = new CastWith(User::class);
    $acc = Data::wrap($data);
    $attribute->handle('users', $acc);

    $rawData = $acc->raw();

    expect($rawData['users'])
        ->toHaveCount(2)
        ->and($rawData['users'][0])
        ->toBeInstanceOf(User::class)
        ->and($rawData['users'][1])
        ->toBeInstanceOf(User::class);
});

it('ignores missing properties during casting', function (): void {
    $data = [
        'not_users' => [],
    ];

    $attribute = new CastWith(User::class);
    $acc = Data::wrap($data);
    $attribute->handle('users', $acc);

    expect($data['not_users'])
        ->toBeArray()
        ->toHaveCount(0);
});

it('skips re-casting for array of DTO instances', function (): void {
    $data = [
        'users' => [
            new User(name: 'Nick', email: 'nick@example.com', address: new Address(street: '123 Main St', city: 'Melbourne', postcode: '3000')),
            new User(name: 'Mike', email: 'mike@example.com', address: new Address(street: '123 Main St', city: 'Melbourne', postcode: '3000')),
        ],
    ];

    $attribute = new CastWith(User::class);
    $acc = Data::wrap($data);
    $attribute->handle('users', $acc);

    expect($data['users'])
        ->toHaveCount(2)
        ->and($data['users'][0])
        ->toBeInstanceOf(User::class)
        ->and($data['users'][1])
        ->toBeInstanceOf(User::class);
});

it('handles non-array value that is already a DTO instance', function (): void {
    $data = [
        'user' => new User(name: 'Nick', email: 'nick@example.com', address: new Address(street: '123 Main St', city: 'Melbourne', postcode: '3000')),
    ];

    $attribute = new CastWith(User::class);
    $acc = Data::wrap($data);
    $attribute->handle('user', $acc);

    expect($data['user'])->toBeInstanceOf(User::class);
});

it('can cast a non-array value into a DTO instance', function (): void {
    $data = [
        'user' => [
            'name' => 'Nick',
            'email' => 'nick@example.com',
            'address' => [
                'street' => '123 Main St',
                'city' => 'Melbourne',
                'postcode' => '3000',
            ],
        ],
    ];

    $attribute = new CastWith(User::class);
    $acc = Data::wrap($data);
    $attribute->handle('user', $acc);

    expect($acc->raw()['user'])
        ->toBeInstanceOf(User::class)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com');
});

it('throws if value is not an array or DTO instance', function (): void {
    $data = [
        'user' => 'not a valid user object or array',
    ];

    $attribute = new CastWith(User::class);

    $acc = Data::wrap($data);
    $attribute->handle('user', $acc);
})->throws(InvalidArgumentException::class, "Unable to cast property 'user' to " . User::class);

it('handles empty array without throwing exception', function (): void {
    $data = [
        'users' => [],
    ];

    $attribute = new CastWith(User::class);
    $acc = Data::wrap($data);
    $attribute->handle('users', $acc);

    expect($data['users'])
        ->toBeArray()
        ->toHaveCount(0);
});

it('throws if invalid caster provided', function (): void {
    new CastWith('not-real');
})->throws(InvalidArgumentException::class, 'Invalid caster type: not-real');
