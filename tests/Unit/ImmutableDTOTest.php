<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use Tests\Fixtures\DTO\Account;
use Tests\Fixtures\DTO\Address;
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
