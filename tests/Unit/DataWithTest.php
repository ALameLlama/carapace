<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Data;
use Error;
use Tests\Fixtures\DTO\Account;
use Tests\Fixtures\DTO\User;

it('can return a new instance with overridden values when using named parameters', function (): void {
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

it('can return a new instance with overridden values when using an array', function (): void {
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

it('can return a new instance with overridden values when using both an array and named parameters', function (): void {
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

it('can return a new instance with overridden values using a CastWith attribute', function (): void {
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

it('can handle empty array', function (): void {
    $dto = User::from([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    $dto2 = $dto->with([]);

    expect($dto)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com');

    expect($dto2)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com');

    expect($dto)->not->toBe($dto2);
});

class emptyDTO extends Data
{
    public function __construct(
    ) {}
}

it('can handle empty dto', function (): void {
    $dto = emptyDTO::from([]);

    $dto2 = $dto->with([]);

    expect($dto)
        ->toBeInstanceOf(emptyDTO::class);

    expect($dto2)
        ->toBeInstanceOf(emptyDTO::class);

    expect($dto)->not->toBe($dto2);
});

it('can return a new instance with overridden values when using an object for overrides', function (): void {
    $dto = User::from([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    $overrides = (object) ['name' => 'Nicholas'];

    $dto2 = $dto->with($overrides);

    expect($dto)
        ->name->toBe('Nick')
        ->email->toBe('nick@example.com');

    expect($dto2)
        ->name->toBe('Nicholas')
        ->email->toBe('nick@example.com');

    expect($dto)->not->toBe($dto2);
});

it('throws when you update the properties directly', function (): void {
    $dto = User::from([
        'name' => 'Nick',
        'email' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ]);

    $dto->name = 'Mick';
})->throws(Error::class, 'Cannot modify readonly property')->skip('Currently setting this as ready only has unintended effects');
