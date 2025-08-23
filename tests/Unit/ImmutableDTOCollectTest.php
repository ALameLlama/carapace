<?php

declare(strict_types=1);

namespace Tests\Unit;

use const JSON_THROW_ON_ERROR;

use ArrayObject;
use JsonException;
use Tests\Fixtures\DTO\User;

it('can collected array into DTO instances', function (): void {
    $data = [
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

    expect($data)
        ->toBeArray();

    $users = User::collect($data);

    expect($users)
        ->toHaveCount(2)
        ->and($users[0])
        ->toBeInstanceOf(User::class)
        ->and($users[1])
        ->toBeInstanceOf(User::class);
});

it('can collected json string into DTO instances', function (): void {
    $data = json_encode([
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
    ], JSON_THROW_ON_ERROR);

    expect($data)
        ->toBeString();

    $users = User::collect($data);

    expect($users)
        ->toHaveCount(2)
        ->and($users[0])
        ->toBeInstanceOf(User::class)
        ->and($users[1])
        ->toBeInstanceOf(User::class);
});

it('throws a JsonException when given invalid JSON', function (): void {
    $invalidJson = '{"name": "Nick", }';

    User::collect($invalidJson);
})->throws(JsonException::class);

it('can collect an array of stdClass objects into DTO instances', function (): void {
    $o1 = (object) [
        'name' => 'Nick',
        'email_address' => 'nick@example.com',
        'address' => (object) [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ];

    $o2 = (object) [
        'name' => 'Mike',
        'email_address' => 'mike@example.com',
        'address' => (object) [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ];

    $users = User::collect([$o1, $o2]);

    expect($users)
        ->toHaveCount(2)
        ->and($users[0])
        ->toBeInstanceOf(User::class)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com')
        ->and($users[1])
        ->toBeInstanceOf(User::class)
        ->name->toBe('Mike')
        ->email->toBe('mike@example.com');
});

it('can collect from a Traversable object (ArrayObject)', function (): void {
    $items = new ArrayObject([
        [
            'name' => 'Nick',
            'email_address' => 'nick@example.com',
            'address' => [
                'street' => '123 Main St',
                'city' => 'Melbourne',
                'postcode' => '3000',
            ],
        ],
        [
            'name' => 'Mike',
            'email_address' => 'mike@example.com',
            'address' => [
                'street' => '123 Main St',
                'city' => 'Melbourne',
                'postcode' => '3000',
            ],
        ],
    ]);

    $users = User::collect($items);

    expect($users)
        ->toHaveCount(2)
        ->and($users[0])
        ->toBeInstanceOf(User::class)
        ->name->toBe('Nick')
        ->and($users[1])
        ->toBeInstanceOf(User::class)
        ->name->toBe('Mike');
});

it('can collect from a generic stdClass container by iterating public properties', function (): void {
    $o1 = (object) [
        'name' => 'Nick',
        'email_address' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ];

    $o2 = (object) [
        'name' => 'Mike',
        'email_address' => 'mike@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ];

    $container = (object) [
        'first' => $o1,
        'second' => $o2,
    ];

    $users = User::collect($container);

    expect($users)
        ->toHaveCount(2)
        ->and($users[0])
        ->toBeInstanceOf(User::class)
        ->name->toBe('Nick')
        ->and($users[1])
        ->toBeInstanceOf(User::class)
        ->name->toBe('Mike');
});
