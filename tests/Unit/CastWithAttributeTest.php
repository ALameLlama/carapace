<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\CastWith;
use InvalidArgumentException;
use Tests\Fixtures\DTO\Address;
use Tests\Fixtures\DTO\User;

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
    $attribute->handleBeforeHydration('users', $data);

    expect($data['users'])
        ->toHaveCount(2)
        ->and($data['users'][0])->toBeInstanceOf(User::class)
        ->and($data['users'][1])->toBeInstanceOf(User::class);
});

it('ignores missing properties during casting', function (): void {
    $data = [
        'not_users' => [],
    ];

    $attribute = new CastWith(User::class);
    $attribute->handleBeforeHydration('users', $data);

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
    $attribute->handleBeforeHydration('users', $data);

    expect($data['users'])
        ->toHaveCount(2)
        ->and($data['users'][0])->toBeInstanceOf(User::class)
        ->and($data['users'][1])->toBeInstanceOf(User::class);
});

it('handles non-array value that is already a DTO instance', function (): void {
    $data = [
        'user' => new User(name: 'Nick', email: 'nick@example.com', address: new Address(street: '123 Main St', city: 'Melbourne', postcode: '3000')),
    ];

    $attribute = new CastWith(User::class);
    $attribute->handleBeforeHydration('user', $data);

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
    $attribute->handleBeforeHydration('user', $data);

    expect($data['user'])->toBeInstanceOf(User::class)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com');
});

it('throws if value is not an array or DTO instance', function (): void {
    $data = [
        'user' => 'not a valid user object or array',
    ];

    $attribute = new CastWith(User::class);

    $attribute->handleBeforeHydration('user', $data);
})->throws(InvalidArgumentException::class, "Unable to cast property 'user' to " . User::class);
