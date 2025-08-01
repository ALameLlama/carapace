<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Fixtures\DTO\Account;
use Tests\Fixtures\DTO\User;

test('using named param returns a new instance with overridden values', function (): void {
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

test('using array returns a new instance with overridden values', function (): void {
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

test('using both array and named params returns a new instance with overridden values', function (): void {
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

test('can use a CastWith attribute returns a new instance with overridden values', function (): void {
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
