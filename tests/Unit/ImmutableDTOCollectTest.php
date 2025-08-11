<?php

declare(strict_types=1);

namespace Tests\Unit;

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
