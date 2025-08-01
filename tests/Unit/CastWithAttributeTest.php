<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use Tests\Fixtures\DTO\Address;
use Tests\Fixtures\DTO\User;

test('can cast nested array of DTOs', function (): void {
    $data['users'] = [
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
    ];

    $attribute = new \Alamellama\Carapace\Attributes\CastWith(User::class);
    $attribute->handle('users', $data);

    expect($data['users'])
        ->toHaveCount(2)
        ->and($data['users'][0])->toBeInstanceOf(User::class)
        ->and($data['users'][1])->toBeInstanceOf(User::class);
});

test('handles missing property when casting', function (): void {
    $data = [
        'not_users' => [],
    ];

    $attribute = new \Alamellama\Carapace\Attributes\CastWith(User::class);
    $attribute->handle('users', $data);

    expect($data['not_users'])
        ->toBeArray()
        ->toHaveCount(0);
});

test('handles array where all items are already caster instances', function (): void {
    $data = [
        'users' => [
            new User(name: 'Nick', email: 'nick@example.com', address: new Address(street: '123 Main St', city: 'Melbourne', postcode: '3000')),
            new User(name: 'Mike', email: 'mike@example.com', address: new Address(street: '123 Main St', city: 'Melbourne', postcode: '3000')),
        ],
    ];

    $attribute = new \Alamellama\Carapace\Attributes\CastWith(User::class);
    $attribute->handle('users', $data);

    expect($data['users'])
        ->toHaveCount(2)
        ->and($data['users'][0])->toBeInstanceOf(User::class)
        ->and($data['users'][1])->toBeInstanceOf(User::class);
});

test('handles non-array value that is already a caster instance', function (): void {
    $casterClass = User::class;
    $data = [
        'user' => new User(name: 'Nick', email: 'nick@example.com', address: new Address(street: '123 Main St', city: 'Melbourne', postcode: '3000')),
    ];

    $attribute = new \Alamellama\Carapace\Attributes\CastWith($casterClass);
    $attribute->handle('user', $data);

    expect($data['user'])->toBeInstanceOf(User::class);
});

test('handles non-array value that requires casting', function (): void {
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

    $attribute = new \Alamellama\Carapace\Attributes\CastWith(User::class);
    $attribute->handle('user', $data);

    expect($data['user'])->toBeInstanceOf(User::class)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com');
});

test('throws if value is not array or caster instance', function (): void {
    $casterClass = User::class;
    $data = [
        'user' => 'not a valid user object or array',
    ];

    $attribute = new \Alamellama\Carapace\Attributes\CastWith($casterClass);

    $attribute->handle('user', $data);
})->throws(InvalidArgumentException::class, "Unable to cast property 'user' to {$casterClass}");
