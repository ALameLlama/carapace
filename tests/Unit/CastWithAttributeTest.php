<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Fixtures\DTO\Account;
use Tests\Fixtures\DTO\User;

test('can cast nested array of DTOs', function (): void {
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
