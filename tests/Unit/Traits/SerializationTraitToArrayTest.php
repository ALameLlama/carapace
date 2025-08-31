<?php

declare(strict_types=1);

namespace Tests\Unit\Traits;

use Alamellama\Carapace\Traits\SerializationTrait;
use Tests\Fixtures\DTO\Account;
use Tests\Fixtures\DTO\Address;
use Tests\Fixtures\DTO\User;

it('can return recursive DTOs', function (): void {
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
        ->address->toBeInstanceOf(Address::class);

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

it('can return a recursive array of DTOs', function (): void {
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

    expect($dto->users[0])
        ->toBeInstanceOf(User::class)
        ->address->toBeInstanceOf(Address::class);

    expect($dto->users[1])
        ->toBeInstanceOf(User::class)
        ->address->toBeInstanceOf(Address::class);

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

it('only includes public properties', function (): void {
    $dto = new class
    {
        use SerializationTrait;

        public string $publicProperty = 'value';

        protected string $protectedProperty = 'protected';

        private string $privateProperty = 'private';
    };

    expect($dto->toArray())
        ->toHaveKey('publicProperty', 'value')
        ->not->toHaveKey('protectedProperty')
        ->not->toHaveKey('privateProperty');
});

it('return empty if no public properties', function (): void {
    $dto = new class
    {
        use SerializationTrait;

        private string $privateProperty = 'private';
    };

    expect($dto->toArray())
        ->toBeArray()
        ->toBeEmpty();
});
