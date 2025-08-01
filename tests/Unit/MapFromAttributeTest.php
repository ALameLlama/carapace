<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\MapFrom;

test('can map attributes from array using MapFrom attribute', function (): void {
    $data = [
        'name' => 'Nick',
        'email_address' => 'nick@example.com',
        'address' => [
            'street' => '123 Main St',
            'city' => 'Melbourne',
            'postcode' => '3000',
        ],
    ];

    $attribute = new MapFrom('email_address');
    $attribute->handle('email', $data);

    expect($data)
        ->toHaveKey('email')
        ->and($data['email'])
        ->toBe('nick@example.com');
});
