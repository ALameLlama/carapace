<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\Fixtures\DTO\Address;
use Tests\Fixtures\DTO\User;

test('can map attributes from array using MapFrom attribute', function (): void {
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
