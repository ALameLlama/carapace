<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use Tests\Fixtures\DTO\Account;
use Tests\Fixtures\DTO\Address;
use Tests\Fixtures\DTO\NoProperty;
use Tests\Fixtures\DTO\Nullable;
use Tests\Fixtures\DTO\RequiredOnly;
use Tests\Fixtures\DTO\User;
use Tests\Fixtures\DTO\WithDefaultValue;

test('from() correctly maps scalar and nested DTOs', function (): void {
    $dto = User::from([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    expect($dto)
        ->toBeInstanceOf(User::class)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com')
        ->address->toBeInstanceOf(Address::class)
        ->address->street->toBe('123 Main St');
});

test('from() uses default value if not provided', function (): void {
    $dto = WithDefaultValue::from([]);

    expect($dto->name)->toBe('Default Nick');
});

test('from() assigns null to nullable parameter when not provided', function (): void {
    $dto = Nullable::from([]);

    expect($dto->optional)->toBeNull();
});

test('from() throws if required parameter is missing', function (): void {
    expect(fn (): RequiredOnly => RequiredOnly::from([]))
        ->toThrow(InvalidArgumentException::class, 'Missing required parameter: required');
});

test('from() constructing DTO with constructor param but no declared property', function (): void {
    $dto = NoProperty::from(['foo' => 'bar']);

    expect($dto)->not->toHaveProperty('foo');
});

test('from() casts to DTOs with CastWith attribute', function (): void {
    $dto = Account::from([
        'name' => 'Me, Myself and I',
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
    ]);

    expect($dto)
        ->name->toBe('Me, Myself and I')
        ->users->toHaveCount(2);

    /** @var User[] $users */
    $users = $dto->users;
    expect($users[0])
        ->toBeInstanceOf(User::class)
        ->name->toBe('Nick');

    expect($users[1])
        ->toBeInstanceOf(User::class)
        ->name->toBe('Mike');
});

test('from() with MapFrom attribute maps correctly', function (): void {
    $dto = User::from([
        'name' => 'Nick',
        'email_address' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    expect($dto)
        ->toBeInstanceOf(User::class)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com')
        ->address->toBeInstanceOf(Address::class)
        ->address->street->toBe('123 Main St');
});

test('with() using named param returns a new instance with overridden values', function (): void {
    $dto = User::from([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    $dto2 = $dto->with(name: 'Nicholas', email: 'nicholas@example');

    expect($dto)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com');

    expect($dto2)
        ->name->toBe('Nicholas')
        ->email->toBe('nicholas@example');

    expect($dto)->not->toBe($dto2);
});

test('with() using array returns a new instance with overridden values', function (): void {
    $dto = User::from([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    $dto2 = $dto->with(['name' => 'Nicholas', 'email' => 'nicholas@example']);

    expect($dto)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com');

    expect($dto2)
        ->name->toBe('Nicholas')
        ->email->toBe('nicholas@example');

    expect($dto)->not->toBe($dto2);
});

test('with() using both array and named params returns a new instance with overridden values', function (): void {
    $dto = User::from([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    $dto2 = $dto->with(['name' => 'Nicholas'], email: 'nicholas@example');

    expect($dto)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com');

    expect($dto2)
        ->name->toBe('Nicholas')
        ->email->toBe('nicholas@example');

    expect($dto)->not->toBe($dto2);
});

// need a with() test for collection of DTOs

test('with() using a CastWith attribute returns a new instance with overridden values', function (): void {
    $dto = Account::from([
        'name' => 'Me, Myself and I',
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
    ]);

    $dto2 = $dto->with(users: [
        [
            'name' => 'Nicholas',
            'email' => 'nicholas@example.com',
            'address' => [
                'street' => '123 Main St',
                'city' => 'Melbourne',
                'postcode' => '3000',
            ],
        ],
    ]);

    expect($dto)
        ->name->toBe('Me, Myself and I')
        ->users->toHaveCount(2);

    expect($dto->users[0])
        ->toBeInstanceOf(User::class)
        ->name->toBe('Nick');

    expect($dto->users[1])
        ->toBeInstanceOf(User::class)
        ->name->toBe('Mike');

    expect($dto2)
        ->name->toBe('Me, Myself and I')
        ->users->toHaveCount(1);

    expect($dto2->users[0])
        ->toBeInstanceOf(User::class)
        ->name->toBe('Nicholas');
});

test('toArray() returns recursive array', function (): void {
    $dto = User::from([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    expect($dto->toArray())->toBe([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);
});

test('toArray() returns recursive array with nested DTOs', function (): void {
    $dto = Account::from([
        'name' => 'Me, Myself and I',
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
    ]);

    expect($dto->toArray())->toBe([
        'name' => 'Me, Myself and I',
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
    ]);
});

test('toJson() returns JSON-encoded output', function (): void {
    $dto = User::from([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    expect($dto->toJson())->toBe(json_encode([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]));
});

test('toJson() returns JSON-encoded output with Nested DTOs', function (): void {
    $dto = Account::from([
        'name' => 'Me, Myself and I',
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
    ]);

    expect($dto->toJson())->toBe(json_encode([
        'name' => 'Me, Myself and I',
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
    ]));
});
