<?php

declare(strict_types=1);

namespace Tests\Unit;

use Alamellama\Carapace\Attributes\MapFrom;

it('can map attributes from an array using MapFrom', function (): void {
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
    $attribute->handleBeforeHydration('email', $data);

    expect($data)
        ->toHaveKey('email')
        ->and($data['email'])
        ->toBe('nick@example.com');
});
